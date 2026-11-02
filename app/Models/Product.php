<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'name', 'slug', 'sku', 'short_description', 'description',
        'brand', 'volume', 'price', 'compare_at_price', 'cost_price', 'stock',
        'image_path', 'is_active', 'is_featured'
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2', 'compare_at_price' => 'decimal:2',
            'cost_price' => 'decimal:2', 'is_active' => 'boolean',
            'is_featured' => 'boolean', 'stock' => 'integer'
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            $product->slug = $product->slug ?: Str::slug($product->name).'-'.Str::lower(Str::random(5));
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
