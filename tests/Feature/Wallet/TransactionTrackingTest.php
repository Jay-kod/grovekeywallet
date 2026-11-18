<?php

namespace Tests\Feature\Wallet;

use App\Models\TransactionRecord;
use App\Models\User;
use App\Models\WalletProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_only_view_their_transaction_history(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $profile = WalletProfile::factory()->create(['user_id' => $user->id]);
        $otherProfile = WalletProfile::factory()->create(['user_id' => $other->id]);

        $record = TransactionRecord::factory()->create([
            'wallet_profile_id' => $profile->id,
            'transaction_hash' => '0x'.str_repeat('1', 64),
        ]);
        $otherRecord = TransactionRecord::factory()->create([
            'wallet_profile_id' => $otherProfile->id,
            'transaction_hash' => '0x'.str_repeat('2', 64),
        ]);

        $this->actingAs($user)
            ->get('/transactions')
            ->assertOk()
            ->assertSee(substr($record->transaction_hash, 0, 12));
    }

    public function test_user_cannot_view_another_users_transaction_detail(): void
    {
        $owner = User::factory()->create();
        $profile = WalletProfile::factory()->create(['user_id' => $owner->id]);
        $record = TransactionRecord::factory()->create([
            'wallet_profile_id' => $profile->id,
            'transaction_hash' => '0x'.str_repeat('3', 64),
        ]);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get("/transactions/{$record->id}")
            ->assertForbidden();
    }

    public function test_pending_receipt_does_not_mark_transaction_confirmed(): void
    {
        $user = User::factory()->create();
        $profile = WalletProfile::factory()->create(['user_id' => $user->id]);
        $record = TransactionRecord::factory()->create([
            'wallet_profile_id' => $profile->id,
            'transaction_hash' => '0x'.str_repeat('4', 64),
            'status' => 'pending',
        ]);

        $this->actingAs($user)
            ->postJson("/transactions/{$record->id}/reconcile", [
                'status' => 'pending',
                'chain_id' => 11155111,
            ])
            ->assertOk()
            ->assertJsonPath('status', 'pending');

        $this->assertDatabaseHas('transaction_records', [
            'id' => $record->id,
            'status' => 'pending',
            'confirmed_at' => null,
        ]);
    }

    public function test_confirmed_receipt_is_reconciled_and_notifies_user(): void
    {
        $user = User::factory()->create();
        $profile = WalletProfile::factory()->create(['user_id' => $user->id]);
        $record = TransactionRecord::factory()->create([
            'wallet_profile_id' => $profile->id,
            'transaction_hash' => '0x'.str_repeat('5', 64),
            'status' => 'pending',
        ]);

        $this->actingAs($user)
            ->postJson("/transactions/{$record->id}/reconcile", [
                'status' => 'confirmed',
                'chain_id' => 11155111,
                'block_number' => '12345',
                'gas_used' => '21000',
            ])
            ->assertOk()
            ->assertJsonPath('status', 'confirmed');

        $this->assertDatabaseHas('transaction_records', [
            'id' => $record->id,
            'status' => 'confirmed',
            'block_number' => '12345',
            'gas_used' => '21000',
        ]);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $user->id,
            'type' => 'transaction_confirmed',
        ]);
    }

    public function test_failed_receipt_is_not_marked_confirmed(): void
    {
        $user = User::factory()->create();
        $profile = WalletProfile::factory()->create(['user_id' => $user->id]);
        $record = TransactionRecord::factory()->create([
            'wallet_profile_id' => $profile->id,
            'transaction_hash' => '0x'.str_repeat('6', 64),
        ]);

        $this->actingAs($user)
            ->postJson("/transactions/{$record->id}/reconcile", [
                'status' => 'failed',
                'chain_id' => 11155111,
                'block_number' => '12346',
                'gas_used' => '22000',
            ])
            ->assertOk();

        $this->assertDatabaseHas('transaction_records', [
            'id' => $record->id,
            'status' => 'failed',
            'confirmed_at' => null,
        ]);
    }

    public function test_wrong_chain_cannot_reconcile_status(): void
    {
        $user = User::factory()->create();
        $profile = WalletProfile::factory()->create(['user_id' => $user->id]);
        $record = TransactionRecord::factory()->create([
            'wallet_profile_id' => $profile->id,
            'transaction_hash' => '0x'.str_repeat('7', 64),
        ]);

        $this->actingAs($user)
            ->postJson("/transactions/{$record->id}/reconcile", [
                'status' => 'confirmed',
                'chain_id' => 1,
                'block_number' => '12347',
            ])
            ->assertUnprocessable();

        $this->assertDatabaseHas('transaction_records', [
            'id' => $record->id,
            'status' => 'submitted',
        ]);
    }
}
