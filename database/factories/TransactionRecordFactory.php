<?php

namespace Database\Factories;

use App\Models\TransactionRecord;
use App\Models\WalletProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TransactionRecord>
 */
class TransactionRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'wallet_profile_id' => WalletProfile::factory(),
            'transaction_hash' => '0x'.fake()->regexify('[a-fA-F0-9]{64}'),
            'from_address' => '0x1111111111111111111111111111111111111111',
            'to_address' => '0x2222222222222222222222222222222222222222',
            'amount_wei' => '1000000000000000000',
            'network_name' => 'Sepolia',
            'chain_id' => 11155111,
            'status' => 'submitted',
            'submitted_at' => now(),
        ];
    }
}
