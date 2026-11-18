<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TransactionController extends Controller
{
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

        return response()->json([
            'message' => 'Transaction submitted. Confirmation is not yet known.',
            'transaction_id' => $record->id,
            'transaction_hash' => $record->transaction_hash,
            'status' => $record->status,
        ], 201);
    }
}
