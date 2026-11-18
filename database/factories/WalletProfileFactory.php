<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\WalletProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WalletProfile>
 */
class WalletProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'address' => fake()->regexify('0x[a-fA-F0-9]{40}'),
            'network_name' => 'Sepolia',
            'chain_id' => 11155111,
        ];
    }
}
