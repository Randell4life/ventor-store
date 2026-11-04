<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'image_path', 'is_active', 'sort_order'];

    protected static function booted(): void
    {
        static::saving(function (Category $category): void {
            if (blank($category->slug) && filled($category->name)) {
                $base = Str::slug($category->name) ?: 'category';
                $slug = $base;
                $suffix = 2;

                while (static::query()->where('slug', $slug)->whereKeyNot($category->getKey())->exists()) {
                    $slug = $base.'-'.$suffix++;
                }

                $category->slug = $slug;
            }
        });
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
