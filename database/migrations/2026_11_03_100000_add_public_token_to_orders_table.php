<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('orders', 'public_token')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('public_token', 64)->nullable();
            });
        }

        if (! Schema::hasIndex('orders', 'orders_public_token_unique')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->unique('public_token', 'orders_public_token_unique');
            });
        }

        DB::table('orders')
            ->whereNull('public_token')
            ->orderBy('id')
            ->chunkById(100, function ($orders) {
                foreach ($orders as $order) {
                    DB::table('orders')
                        ->where('id', $order->id)
                        ->update([
                            'public_token' => Str::random(64),
                        ]);
                }
            });
    }

    public function down(): void
    {
        if (Schema::hasIndex('orders', 'orders_public_token_unique')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropUnique('orders_public_token_unique');
            });
        }

        if (Schema::hasColumn('orders', 'public_token')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('public_token');
            });
        }
    }
};
