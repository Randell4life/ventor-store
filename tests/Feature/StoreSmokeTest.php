<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_is_available(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('VENTOR');
    }

    public function test_admin_login_page_is_available(): void
    {
        $this->get('/admin/login')->assertOk();
    }

    public function test_non_admin_user_cannot_access_admin_panel(): void
    {
        $user = User::create([
            'name' => 'Store Customer',
            'email' => 'customer@example.com',
            'password' => 'CustomerPassword123!',
            'is_admin' => false,
        ]);

        $this->actingAs($user)
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_admin_user_can_access_admin_dashboard(): void
    {
        $admin = User::create([
            'name' => 'VENTOR Admin',
            'email' => 'admin@example.com',
            'password' => 'SecureAdminPassword123!',
            'is_admin' => true,
        ]);

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk();
    }
}
