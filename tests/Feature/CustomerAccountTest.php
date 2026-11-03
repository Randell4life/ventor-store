<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_register_and_is_not_an_admin(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
            'phone' => '555123456',
            'password' => 'StrongPassword123!',
            'password_confirmation' => 'StrongPassword123!',
        ]);

        $response->assertRedirect('/account');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'customer@example.com',
            'is_admin' => false,
            'role' => 'customer',
        ]);
    }

    public function test_customer_can_log_in(): void
    {
        User::create([
            'name' => 'Existing Customer',
            'email' => 'existing@example.com',
            'password' => Hash::make('StrongPassword123!'),
            'is_admin' => false,
        ]);

        $this->post('/login', [
            'email' => 'existing@example.com',
            'password' => 'StrongPassword123!',
        ])->assertRedirect('/account');

        $this->assertAuthenticated();
    }

    public function test_account_is_protected_from_guests(): void
    {
        $this->get('/account')->assertRedirect('/login');
    }
}
