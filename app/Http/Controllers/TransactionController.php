<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\TransactionRecord;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $records = TransactionRecord::query()
            ->whereHas('walletProfile', fn ($query) => $query->where('user_id', $request->user()->id))
            ->latest('submitted_at')
            ->latest('created_at')
            ->get();

        return view('transactions.index', compact('records'));
    }

    public function show(Request $request, TransactionRecord $transactionRecord)
    {
        abort_unless($transactionRecord->walletProfile->user_id === $request->user()->id, 403);

        return view('transactions.show', compact('transactionRecord'));
    }

    public function create(Request $request)
    {
        $walletProfiles = $request->user()->walletProfiles()->latest()->get();

        abort_if($walletProfiles->isEmpty(), 409, 'Connect a Sepolia wallet before preparing a transaction.');

        return view('transactions.create', compact('walletProfiles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'wallet_profile_id' => ['required', 'integer'],
            'from_address' => ['required', 'string', 'regex:/^0x[a-fA-F0-9]{40}$/'],
            'to_address' => ['required', 'string', 'regex:/^0x[a-fA-F0-9]{40}$/'],
            'amount_wei' => ['required', 'regex:/^[1-9][0-9]*$/'],
            'transaction_hash' => ['required', 'string', 'regex:/^0x[a-fA-F0-9]{64}$/', 'unique:transaction_records,transaction_hash'],
            'chain_id' => ['required', 'integer', Rule::in([11155111])],
            'gas_limit' => ['nullable', 'regex:/^[0-9]+$/'],
            'gas_price_wei' => ['nullable', 'regex:/^[0-9]+$/'],
        ]);

        $profile = $request->user()->walletProfiles()->findOrFail($validated['wallet_profile_id']);

        abort_unless(
            strtolower($profile->address) === strtolower($validated['from_address']),
            403,
            'The transaction sender must match the connected wallet.',
        );

        $record = $profile->transactionRecords()->create([
            'transaction_hash' => strtolower($validated['transaction_hash']),
            'from_address' => strtolower($validated['from_address']),
            'to_address' => strtolower($validated['to_address']),
            'amount_wei' => $validated['amount_wei'],
            'network_name' => 'Sepolia',
            'chain_id' => 11155111,
            'gas_limit' => $validated['gas_limit'] ?? null,
            'gas_price_wei' => $validated['gas_price_wei'] ?? null,
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $request->user()->notifications()->create([
            'type' => 'transaction_submitted',
            'message' => 'Transaction submitted. Network confirmation is pending.',
            'related_transaction_hash' => $record->transaction_hash,
        ]);

        return response()->json([
            'message' => 'Transaction submitted. Confirmation is not yet known.',
            'transaction_id' => $record->id,
            'transaction_hash' => $record->transaction_hash,
            'status' => $record->status,
        ], 201);
    }

    public function reconcile(Request $request, TransactionRecord $transactionRecord)
    {
        abort_unless($transactionRecord->walletProfile->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(['submitted', 'pending', 'confirmed', 'failed', 'reverted', 'unknown'])],
            'chain_id' => ['required', 'integer', Rule::in([11155111])],
            'block_number' => ['nullable', 'regex:/^[0-9]+$/'],
            'gas_used' => ['nullable', 'regex:/^[0-9]+$/'],
        ]);

        $previousStatus = $transactionRecord->status;

        $updates = [
            'status' => $validated['status'],
            'block_number' => $validated['block_number'] ?? null,
            'gas_used' => $validated['gas_used'] ?? null,
            'last_checked_at' => now(),
        ];

        if ($validated['status'] === 'confirmed') {
            $updates['confirmed_at'] = now();
        }

        $transactionRecord->update($updates);

        if ($previousStatus !== 'confirmed' && $validated['status'] === 'confirmed') {
            $request->user()->notifications()->create([
                'type' => 'transaction_confirmed',
                'message' => 'Transaction confirmed on Ethereum Sepolia.',
                'related_transaction_hash' => $transactionRecord->transaction_hash,
            ]);
        }

        if ($previousStatus !== 'failed' && in_array($validated['status'], ['failed', 'reverted'], true)) {
            $request->user()->notifications()->create([
                'type' => 'transaction_failed',
                'message' => 'Transaction failed or reverted on Ethereum Sepolia.',
                'related_transaction_hash' => $transactionRecord->transaction_hash,
            ]);
        }

        return response()->json([
            'status' => $transactionRecord->status,
            'block_number' => $transactionRecord->block_number,
            'gas_used' => $transactionRecord->gas_used,
            'confirmed_at' => optional($transactionRecord->confirmed_at)->toIso8601String(),
        ]);
    }

    public function notifications(Request $request)
    {
        return view('transactions.notifications', [
            'notifications' => $request->user()->notifications()->latest()->get(),
        ]);
    }

    public function markNotificationRead(Request $request, Notification $notification)
    {
        abort_unless($notification->user_id === $request->user()->id, 403);
        $notification->update(['read_at' => now()]);

        return response()->json(['message' => 'Notification marked as read.']);
    }
}
