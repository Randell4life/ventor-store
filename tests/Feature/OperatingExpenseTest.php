<?php

namespace Tests\Feature;

use App\Models\OperatingExpense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OperatingExpenseTest extends TestCase
{
    use RefreshDatabase;

    public function test_operating_expense_records_paid_and_unpaid_costs_separately(): void
    {
        $user = User::create([
            'name' => 'VENTOR Admin',
            'email' => 'expense-admin@example.com',
            'password' => 'SecurePassword123!',
            'is_admin' => true,
        ]);

        $paid = OperatingExpense::create([
            'created_by' => $user->id,
            'category' => 'rent',
            'description' => 'ოფისის ქირა',
            'amount' => 1200,
            'expense_date' => '2026-11-01',
            'status' => 'paid',
            'payment_method' => 'bank_transfer',
            'paid_at' => '2026-11-02',
        ]);

        $unpaid = OperatingExpense::create([
            'created_by' => $user->id,
            'category' => 'advertising',
            'description' => 'სარეკლამო კამპანია',
            'amount' => 350,
            'expense_date' => '2026-11-03',
            'status' => 'unpaid',
        ]);

        $expenses = OperatingExpense::query()->get();

        $this->assertSame('1200.00', $paid->fresh()->amount);
        $this->assertSame('paid', $paid->fresh()->status);
        $this->assertSame('unpaid', $unpaid->fresh()->status);
        $this->assertEquals(1550.0, (float) $expenses->sum('amount'));
        $this->assertEquals(1200.0, (float) $expenses->where('status', 'paid')->sum('amount'));
        $this->assertEquals(350.0, (float) $expenses->where('status', 'unpaid')->sum('amount'));
        $this->assertSame('VENTOR Admin', $paid->fresh()->creator->name);
    }

    public function test_admin_can_open_operating_expenses_page(): void
    {
        $admin = User::create([
            'name' => 'VENTOR Admin',
            'email' => 'expense-page-admin@example.com',
            'password' => 'SecurePassword123!',
            'is_admin' => true,
        ]);

        $this->actingAs($admin)
            ->get('/admin/operating-expenses')
            ->assertOk()
            ->assertSee('საოპერაციო ხარჯები');
    }
}
