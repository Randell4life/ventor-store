<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'role')) {
                $table->string('role', 32)->default('customer');
            }

            if (! Schema::hasColumn('users', 'customer_type')) {
                $table->string('customer_type', 24)->default('retail');
            }

            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 40)->nullable();
            }

            if (! Schema::hasColumn('users', 'company_name')) {
                $table->string('company_name', 180)->nullable();
            }

            if (! Schema::hasColumn('users', 'tax_number')) {
                $table->string('tax_number', 64)->nullable();
            }
        });

        if (! Schema::hasIndex('users', 'users_role_index')) {
            Schema::table('users', function (Blueprint $table) {
                $table->index('role', 'users_role_index');
            });
        }

        DB::table('users')->where('is_admin', true)->update(['role' => 'admin']);

        if (! Schema::hasColumn('orders', 'user_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('orders', 'user_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropConstrainedForeignId('user_id');
            });
        }

        if (Schema::hasIndex('users', 'users_role_index')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex('users_role_index');
            });
        }

        Schema::table('users', function (Blueprint $table) {
            foreach (['tax_number', 'company_name', 'phone', 'customer_type', 'role'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
