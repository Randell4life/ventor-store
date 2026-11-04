<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'loyalty_points')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('loyalty_points')->default(0);
            });
        }

        if (! Schema::hasColumn('orders', 'loyalty_points_earned')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->unsignedInteger('loyalty_points_earned')->default(0);
            });
        }

        if (! Schema::hasColumn('orders', 'loyalty_points_awarded_at')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->timestamp('loyalty_points_awarded_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        foreach (['loyalty_points_awarded_at', 'loyalty_points_earned'] as $column) {
            if (Schema::hasColumn('orders', $column)) {
                Schema::table('orders', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }

        if (Schema::hasColumn('users', 'loyalty_points')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('loyalty_points');
            });
        }
    }
};
