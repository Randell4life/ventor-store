<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_can_filter_by_brand_and_in_stock(): void
    {
        Product::create([
            'name' => 'Premium Chain Cleaner',
            'slug' => 'premium-chain-cleaner',
            'brand' => 'VENTOR',
            'price' => 29.90,
            'stock' => 8,
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'Out of Stock Polish',
            'slug' => 'out-of-stock-polish',
            'brand' => 'OTHER',
            'price' => 19.90,
            'stock' => 0,
            'is_active' => true,
        ]);

        $this->get('/catalog?brand=VENTOR&in_stock=1')
            ->assertOk()
            ->assertSee('Premium Chain Cleaner')
            ->assertDontSee('Out of Stock Polish')
            ->assertSee('ნაპოვნია');
    }

    public function test_catalog_search_matches_sku_and_short_description(): void
    {
        Product::create([
            'name' => 'Engine Treatment',
            'slug' => 'engine-treatment',
            'sku' => 'VT-ENG-100',
            'short_description' => 'Engine protection formula',
            'price' => 39.00,
            'stock' => 4,
            'is_active' => true,
        ]);

        $this->get('/catalog?q=VT-ENG-100')
            ->assertOk()
            ->assertSee('Engine Treatment');

        $this->get('/catalog?q=protection')
            ->assertOk()
            ->assertSee('Engine Treatment');
    }

    public function test_product_page_displays_related_products_from_same_category(): void
    {
        $category = Category::create([
            'name' => 'Car Care',
            'slug' => 'car-care',
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Ceramic Detailer',
            'slug' => 'ceramic-detailer',
            'price' => 45.00,
            'stock' => 5,
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Interior Cleaner',
            'slug' => 'interior-cleaner',
            'price' => 25.00,
            'stock' => 7,
            'is_active' => true,
        ]);

        $this->get('/product/ceramic-detailer')
            ->assertOk()
            ->assertSee('Ceramic Detailer')
            ->assertSee('მსგავსი პროდუქტები')
            ->assertSee('Interior Cleaner');
    }
}
