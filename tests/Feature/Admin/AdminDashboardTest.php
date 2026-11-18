<?php

namespace Tests\Feature\Admin;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_administrator_routes(): void
    {
        $this->get('/admin')->assertRedirect('/login');
        $this->get('/admin/users')->assertRedirect('/login');
        $this->get('/admin/logs')->assertRedirect('/login');
    }

    public function test_regular_user_cannot_access_administrator_routes(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin')->assertForbidden();
        $this->actingAs($user)->get('/admin/users')->assertForbidden();
        $this->actingAs($user)->get('/admin/logs')->assertForbidden();
    }

    public function test_administrator_can_view_operational_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Operational health')
            ->assertSee('Ethereum Sepolia')
            ->assertSee('11155111');
    }

    public function test_administrator_can_suspend_another_user_and_audit_it(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $this->actingAs($admin)
            ->patch("/admin/users/{$user->id}/status", ['status' => 'suspended'])
            ->assertRedirect(route('admin.users'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'status' => 'suspended',
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $admin->id,
            'event_type' => 'account_status_changed',
            'outcome' => 'success',
        ]);
    }

    public function test_administrator_cannot_suspend_self(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->patch("/admin/users/{$admin->id}/status", ['status' => 'suspended'])
            ->assertStatus(422);

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'status' => 'active',
        ]);
    }

    public function test_administrator_can_update_only_allowlisted_non_secret_settings(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->patch('/admin/settings', [
                'maintenance_notice' => 'Scheduled maintenance',
                'support_status' => 'maintenance',
            ])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertDatabaseHas('application_settings', [
            'setting_key' => 'maintenance_notice',
            'setting_value' => 'Scheduled maintenance',
            'updated_by' => $admin->id,
        ]);
        $this->assertDatabaseMissing('application_settings', [
            'setting_key' => 'rpc_api_key',
        ]);
    }

    public function test_audit_log_page_contains_no_wallet_secrets(): void
    {
        $admin = User::factory()->admin()->create();
        AuditLog::create([
            'actor_id' => $admin->id,
            'event_type' => 'account_status_changed',
            'outcome' => 'success',
            'context' => ['target_user_id' => 99, 'status' => 'active'],
        ]);

        $this->actingAs($admin)
            ->get('/admin/logs')
            ->assertOk()
            ->assertSee('account_status_changed')
            ->assertDontSee('private_key')
            ->assertDontSee('seed_phrase');
    }
}
