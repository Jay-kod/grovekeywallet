<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_open_administrator_dashboard(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_regular_user_cannot_open_administrator_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_active_administrator_can_open_administrator_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Operational health');
    }

    public function test_suspended_administrator_cannot_open_administrator_dashboard(): void
    {
        $admin = User::factory()->admin()->suspended()->create();

        $this->actingAs($admin)->get('/admin')->assertForbidden();
    }

    public function test_protected_dashboard_requires_authentication(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_registration_does_not_allow_admin_role_selection(): void
    {
        $this->post('/register', [
            'name' => 'Test User',
            'email' => 'role-test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'role-test@example.com',
            'role' => 'user',
            'status' => 'active',
        ]);
    }
}
