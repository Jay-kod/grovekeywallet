<?php

namespace Tests\Feature\Wallet;

use App\Models\User;
use App\Models\WalletProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalletConnectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_open_wallet_page(): void
    {
        $this->get('/wallet')->assertRedirect('/login');
    }

    public function test_user_can_save_a_sepolia_public_address(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/wallet/connect', [
                'address' => '0xAbCDEF1234567890ABCDEF1234567890abcdef12',
                'chain_id' => 11155111,
            ])
            ->assertOk()
            ->assertJsonPath('wallet.address', '0xabcdef1234567890abcdef1234567890abcdef12')
            ->assertJsonPath('wallet.network_name', 'Sepolia')
            ->assertJsonPath('wallet.chain_id', 11155111);

        $this->assertDatabaseHas('wallet_profiles', [
            'user_id' => $user->id,
            'address' => '0xabcdef1234567890abcdef1234567890abcdef12',
            'network_name' => 'Sepolia',
            'chain_id' => 11155111,
        ]);
    }

    public function test_wrong_network_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/wallet/connect', [
                'address' => '0xabcdef1234567890abcdef1234567890abcdef12',
                'chain_id' => 1,
            ])
            ->assertUnprocessable();

        $this->assertDatabaseCount('wallet_profiles', 0);
    }

    public function test_malformed_address_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/wallet/connect', [
                'address' => 'not-an-address',
                'chain_id' => 11155111,
            ])
            ->assertUnprocessable();

        $this->assertDatabaseCount('wallet_profiles', 0);
    }

    public function test_user_can_remove_only_their_own_wallet_link(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $profile = WalletProfile::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $this->actingAs($owner)
            ->deleteJson("/wallet/{$profile->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('wallet_profiles', ['id' => $profile->id]);

        $this->actingAs($otherUser)
            ->deleteJson("/wallet/{$profile->id}")
            ->assertOk();

        $this->assertDatabaseMissing('wallet_profiles', ['id' => $profile->id]);
    }

    public function test_wallet_page_lists_only_the_authenticated_users_profiles(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        WalletProfile::factory()->create([
            'user_id' => $user->id,
            'address' => '0x1111111111111111111111111111111111111111',
        ]);
        WalletProfile::factory()->create([
            'user_id' => $otherUser->id,
            'address' => '0x2222222222222222222222222222222222222222',
        ]);

        $this->actingAs($user)
            ->get('/wallet')
            ->assertOk()
            ->assertSee('0x1111111111111111111111111111111111111111')
            ->assertDontSee('0x2222222222222222222222222222222222222222');
    }

    public function test_wallet_page_labels_live_balance_as_sepolia_test_eth(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/wallet')
            ->assertOk()
            ->assertSee('Sepolia test-ETH balance')
            ->assertSee('Refresh balance');
    }

    public function test_dashboard_shows_sepolia_balance_states(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Sepolia wallet')
            ->assertSee('Sepolia test-ETH balance')
            ->assertSee('Refresh balance');
    }
}
