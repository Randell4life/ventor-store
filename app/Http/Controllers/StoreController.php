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
            $term = $request->string('q')->toString();
            $query->where(fn ($q) => $q->where('name', 'like', "%{$term}%")
                ->orWhere('brand', 'like', "%{$term}%")
                ->orWhere('sku', 'like', "%{$term}%"));
        }
        if ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->category));
        }
        if ($request->get('sort') === 'price_asc') $query->orderBy('price');
        elseif ($request->get('sort') === 'price_desc') $query->orderByDesc('price');
        else $query->latest();

        return view('store.catalog', [
            'products' => $query->paginate(12)->withQueryString(),
            'categories' => Category::where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);
        return view('store.product', compact('product'));
    }
}
