<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $adminEmail = env('VENTOR_ADMIN_EMAIL');
        $adminPassword = env('VENTOR_ADMIN_PASSWORD');

        if (blank($adminEmail) || blank($adminPassword)) {
            throw new RuntimeException(
                'Set VENTOR_ADMIN_EMAIL and VENTOR_ADMIN_PASSWORD in .env before running database seeders.'
            );
        }

        User::firstOrCreate(['email' => $adminEmail], [
            'name' => 'VENTOR Admin',
            'password' => $adminPassword,
            'is_admin' => true,
        ]);

        $care = Category::firstOrCreate(
            ['slug' => 'car-care'],
            ['name' => 'ავტომობილის მოვლა', 'sort_order' => 1]
        );

        $moto = Category::firstOrCreate(
            ['slug' => 'moto-care'],
            ['name' => 'მოტოციკლის მოვლა', 'sort_order' => 2]
        );

        Product::firstOrCreate(['slug' => 'demo-cleaner'], [
            'category_id' => $care->id,
            'name' => 'სატესტო საწმენდი საშუალება',
            'short_description' => 'სატესტო ჩანაწერი — ჩაანაცვლეთ რეალური ICECO პროდუქციით.',
            'price' => 0,
            'stock' => 0,
            'is_active' => false,
        ]);

        Product::firstOrCreate(['slug' => 'demo-moto-care'], [
            'category_id' => $moto->id,
            'name' => 'სატესტო მოტო მოვლის პროდუქტი',
            'short_description' => 'სატესტო ჩანაწერი — ჩაანაცვლეთ რეალური პროდუქციით.',
            'price' => 0,
            'stock' => 0,
            'is_active' => false,
        ]);
    }
}
