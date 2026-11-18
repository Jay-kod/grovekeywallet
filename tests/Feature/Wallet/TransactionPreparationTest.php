<?php

namespace Tests\Feature\Wallet;

use App\Models\User;
use App\Models\WalletProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionPreparationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_open_transaction_form(): void
    {
        $this->get('/transactions/create')->assertRedirect('/login');
    }

    public function test_user_without_wallet_cannot_prepare_a_transaction(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/transactions/create')
            ->assertStatus(409);
    }

    public function test_user_can_view_transaction_review_form_after_connecting_wallet(): void
    {
        $user = User::factory()->create();
        WalletProfile::factory()->create([
            'user_id' => $user->id,
            'address' => '0x1111111111111111111111111111111111111111',
        ]);

        $this->actingAs($user)
            ->get('/transactions/create')
            ->assertOk()
            ->assertSee('Send Sepolia test ETH')
            ->assertSee('Confirm in wallet');
    }

    public function test_authorized_submission_records_only_the_validated_hash_and_metadata(): void
    {
        $user = User::factory()->create();
        $profile = WalletProfile::factory()->create([
            'user_id' => $user->id,
            'address' => '0x1111111111111111111111111111111111111111',
        ]);
        $hash = '0x'.str_repeat('a', 64);

        $this->actingAs($user)
            ->postJson('/transactions', [
                'wallet_profile_id' => $profile->id,
                'from_address' => '0x1111111111111111111111111111111111111111',
                'to_address' => '0x2222222222222222222222222222222222222222',
                'amount_wei' => '1000000000000000000',
                'transaction_hash' => $hash,
                'chain_id' => 11155111,
                'gas_limit' => '21000',
                'gas_price_wei' => '1000000000',
            ])
            ->assertCreated()
            ->assertJsonPath('status', 'submitted')
            ->assertJsonPath('transaction_hash', $hash);

        $this->assertDatabaseHas('transaction_records', [
            'wallet_profile_id' => $profile->id,
            'transaction_hash' => $hash,
            'status' => 'submitted',
            'chain_id' => 11155111,
        ]);
    }

    public function test_wrong_chain_is_rejected(): void
    {
        $user = User::factory()->create();
        $profile = WalletProfile::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->postJson('/transactions', [
                'wallet_profile_id' => $profile->id,
                'from_address' => $profile->address,
                'to_address' => '0x2222222222222222222222222222222222222222',
                'amount_wei' => '100',
                'transaction_hash' => '0x'.str_repeat('b', 64),
                'chain_id' => 1,
            ])
            ->assertUnprocessable();

        $this->assertDatabaseCount('transaction_records', 0);
    }

    public function test_sender_must_match_owned_wallet_profile(): void
    {
        $owner = User::factory()->create();
        $profile = WalletProfile::factory()->create([
            'user_id' => $owner->id,
            'address' => '0x1111111111111111111111111111111111111111',
        ]);

        $this->actingAs($owner)
            ->postJson('/transactions', [
                'wallet_profile_id' => $profile->id,
                'from_address' => '0x3333333333333333333333333333333333333333',
                'to_address' => '0x2222222222222222222222222222222222222222',
                'amount_wei' => '100',
                'transaction_hash' => '0x'.str_repeat('c', 64),
                'chain_id' => 11155111,
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('transaction_records', 0);
    }

    public function test_user_cannot_submit_against_another_users_wallet_profile(): void
    {
        $otherUser = User::factory()->create();
        $profile = WalletProfile::factory()->create(['user_id' => $otherUser->id]);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/transactions', [
                'wallet_profile_id' => $profile->id,
                'from_address' => $profile->address,
                'to_address' => '0x2222222222222222222222222222222222222222',
                'amount_wei' => '100',
                'transaction_hash' => '0x'.str_repeat('d', 64),
                'chain_id' => 11155111,
            ])
            ->assertNotFound();

        $this->assertDatabaseCount('transaction_records', 0);
    }
}
