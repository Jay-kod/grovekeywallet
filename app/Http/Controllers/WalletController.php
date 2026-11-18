<?php

namespace App\Http\Controllers;

use App\Models\WalletProfile;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WalletController extends Controller
{
    public function index(Request $request)
    {
        return view('wallet.index', [
            'walletProfiles' => $request->user()->walletProfiles()->latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'address' => ['required', 'string', 'regex:/^0x[a-fA-F0-9]{40}$/'],
            'chain_id' => ['required', 'integer', Rule::in([11155111])],
        ]);

        $address = strtolower($validated['address']);

        $profile = $request->user()->walletProfiles()->updateOrCreate(
            ['address' => $address],
            [
                'network_name' => 'Sepolia',
                'chain_id' => 11155111,
            ],
        );

        return response()->json([
            'message' => 'Wallet connected to Ethereum Sepolia.',
            'wallet' => [
                'id' => $profile->id,
                'address' => $profile->address,
                'network_name' => $profile->network_name,
                'chain_id' => $profile->chain_id,
            ],
        ]);
    }

    public function destroy(Request $request, WalletProfile $walletProfile)
    {
        abort_unless($walletProfile->user_id === $request->user()->id, 403);

        $walletProfile->delete();

        return response()->json(['message' => 'Wallet link removed.']);
    }
}
