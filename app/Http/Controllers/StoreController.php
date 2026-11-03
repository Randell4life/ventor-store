<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function home()
    {
        return view('store.home', [
            'categories' => Category::where('is_active', true)->orderBy('sort_order')->get(),
            'products' => Product::with('category')->where('is_active', true)->latest()->take(8)->get(),
            'featured' => Product::with('category')->where('is_active', true)->where('is_featured', true)->take(4)->get(),
        ]);
    }

    public function catalog(Request $request)
    {
        $query = Product::with('category')->where('is_active', true);

        if ($request->filled('q')) {
            $term = trim($request->string('q')->toString());
            $query->where(fn ($q) => $q->where('name', 'like', "%{$term}%")
                ->orWhere('brand', 'like', "%{$term}%")
                ->orWhere('sku', 'like', "%{$term}%")
                ->orWhere('short_description', 'like', "%{$term}%"));
        }

        if ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->string('category')->toString()));
        }

        if ($request->filled('brand')) {
            $query->where('brand', $request->string('brand')->toString());
        }

        if ($request->boolean('in_stock')) {
            $query->where('stock', '>', 0);
        }

        match ($request->get('sort')) {
            'price_asc' => $query->orderBy('price')->orderBy('name'),
            'price_desc' => $query->orderByDesc('price')->orderBy('name'),
            'name_asc' => $query->orderBy('name'),
            default => $query->latest(),
        };

        return view('store.catalog', [
            'products' => $query->paginate(12)->withQueryString(),
            'categories' => Category::where('is_active', true)->orderBy('sort_order')->get(),
            'brands' => Product::where('is_active', true)->whereNotNull('brand')
                ->where('brand', '!=', '')->distinct()->orderBy('brand')->pluck('brand'),
        ]);
    }

    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);

        $product->load('category');
        $relatedProducts = Product::with('category')
            ->where('is_active', true)
            ->where('id', '!=', $product->id)
            ->when($product->category_id, fn ($query) => $query->where('category_id', $product->category_id))
            ->latest()
            ->take(4)
            ->get();

        return view('store.product', compact('product', 'relatedProducts'));
    }
}
