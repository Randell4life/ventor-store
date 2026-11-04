<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('orders', 'loyalty_points_earned')) {
            Schema::table('orders', function (Blueprint $table): void {
                $table->unsignedInteger('loyalty_points_earned')->default(0);
            });
        }

        if (! Schema::hasColumn('orders', 'loyalty_points_redeemed')) {
            Schema::table('orders', function (Blueprint $table): void {
                $table->unsignedInteger('loyalty_points_redeemed')->default(0);
            });
        }

        if (! Schema::hasColumn('orders', 'loyalty_points_awarded_at')) {
            Schema::table('orders', function (Blueprint $table): void {
                $table->timestamp('loyalty_points_awarded_at')->nullable();
            });
        }

        if (! Schema::hasColumn('users', 'loyalty_points')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->unsignedBigInteger('loyalty_points')->default(0);
            });
        }
    }

    public function down(): void
    {
        // Intentionally non-destructive: loyalty balances and order history must be preserved.
    }
};
