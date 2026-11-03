<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $care = Category::firstOrCreate(
            ['slug' => 'car-care'],
            ['name' => 'ავტომობილის მოვლა', 'sort_order' => 1],
        );

        $moto = Category::firstOrCreate(
            ['slug' => 'moto-care'],
            ['name' => 'მოტოციკლის მოვლა', 'sort_order' => 2],
        );

        Product::firstOrCreate(['slug' => 'demo-cleaner'], [
            'category_id' => $care->id,
            'name' => 'სატესტო საწმენდი საშუალება',
            'short_description' => 'სატესტო ჩანაწერი — ჩაანაცვლეთ რეალური პროდუქციით.',
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
