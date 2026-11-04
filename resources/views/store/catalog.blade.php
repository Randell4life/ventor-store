@extends('layouts.store')
@push('head')
<link rel="stylesheet" href="{{ asset('css/storefront-premium.css') }}">
<style>
.catalog-toolbar{display:grid;grid-template-columns:minmax(220px,2fr) repeat(3,minmax(150px,1fr)) auto;gap:10px;margin:24px 0 16px;padding:16px;background:var(--panel);border:1px solid var(--line)}
.catalog-toolbar input,.catalog-toolbar select{width:100%;min-width:0;background:#0b1421;color:var(--text);border:1px solid var(--line);padding:13px;border-radius:3px}
.catalog-toolbar label{font-size:11px;color:var(--muted);display:block;margin:0 0 6px}
.catalog-meta{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin:14px 0 22px;color:var(--muted);font-size:12px}
.stock-toggle{display:flex!important;align-items:center;gap:8px;height:44px;white-space:nowrap}
.stock-toggle input{width:16px}
.filter-chips{display:flex;gap:8px;flex-wrap:wrap;margin:0 0 22px}
.filter-chip{border:1px solid var(--line);padding:7px 11px;color:var(--ice);font-size:12px}
.catalog-empty{padding:45px 20px;text-align:center;border:1px dashed var(--line);background:var(--panel)}
@media(max-width:900px){.catalog-toolbar{grid-template-columns:repeat(2,minmax(0,1fr))}.catalog-toolbar .search-field{grid-column:1/-1}}
@media(max-width:560px){.catalog-toolbar{grid-template-columns:1fr;padding:11px}.catalog-toolbar .search-field{grid-column:auto}.catalog-toolbar .btn{width:100%}}
</style>
@endpush
@section('title','კატალოგი — VENTOR')
@section('content')
<section class="section catalog-page">
    <div class="catalog-hero">
        <div class="wrap catalog-hero-inner">
            <div><div class="eyebrow">VENTOR / PRODUCT COLLECTION</div><h1>პროდუქცია, რომელიც<br><em>ზრუნვას ამარტივებს.</em></h1><p>შეარჩიე ავტომობილის მოვლისა და წმენდის საშუალებები — შენი ყოველდღიური საჭიროებების მიხედვით.</p></div>
            <div class="catalog-hero-mark"><span>V</span><small>PROFESSIONAL<br>CARE SYSTEM</small></div>
        </div>
    </div>
    <div class="wrap catalog-content">
        <div class="catalog-intro-row"><div><div class="eyebrow">01 / EXPLORE THE RANGE</div><h2>იპოვე საჭირო პროდუქტი</h2></div><span class="catalog-count">{{ $products->total() }} <small>პროდუქტი</small></span></div>

        <form class="catalog-toolbar" method="GET" action="{{ route('catalog') }}">
            <div class="search-field">
                <label for="catalog-q">პროდუქტის ძიება</label>
                <input id="catalog-q" type="search" name="q" value="{{ request('q') }}" placeholder="სახელი, ბრენდი, SKU...">
            </div>
            <div>
                <label for="catalog-category">კატეგორია</label>
                <select id="catalog-category" name="category">
                    <option value="">ყველა კატეგორია</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->slug }}" @selected(request('category')===$category->slug)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="catalog-brand">ბრენდი</label>
                <select id="catalog-brand" name="brand">
                    <option value="">ყველა ბრენდი</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand }}" @selected(request('brand')===$brand)>{{ $brand }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="catalog-sort">დალაგება</label>
                <select id="catalog-sort" name="sort">
                    <option value="">უახლესი</option>
                    <option value="price_asc" @selected(request('sort')==='price_asc')>ფასი: დაბლიდან მაღლა</option>
                    <option value="price_desc" @selected(request('sort')==='price_desc')>ფასი: მაღლიდან დაბლა</option>
                    <option value="name_asc" @selected(request('sort')==='name_asc')>სახელით: A–Z</option>
                </select>
            </div>
            <div style="display:flex;flex-direction:column;justify-content:end;gap:8px">
                <label class="stock-toggle"><input type="checkbox" name="in_stock" value="1" @checked(request()->boolean('in_stock'))> მხოლოდ მარაგში</label>
                <button class="btn" type="submit">ძიება / ფილტრი</button>
            </div>
        </form>

        <div class="catalog-meta">
            <span>ნაპოვნია <strong style="color:var(--text)">{{ $products->total() }}</strong> პროდუქტი</span>
            @if(request()->hasAny(['q','category','brand','sort','in_stock']))
                <a href="{{ route('catalog') }}" style="color:var(--ice)">ფილტრების გასუფთავება ×</a>
            @endif
        </div>

        @if(request()->hasAny(['q','category','brand','in_stock']))
            <div class="filter-chips">
                @if(request('q'))<span class="filter-chip">ძიება: {{ request('q') }}</span>@endif
                @if(request('category'))<span class="filter-chip">კატეგორია: {{ $categories->firstWhere('slug',request('category'))?->name ?? request('category') }}</span>@endif
                @if(request('brand'))<span class="filter-chip">ბრენდი: {{ request('brand') }}</span>@endif
                @if(request()->boolean('in_stock'))<span class="filter-chip">მარაგშია</span>@endif
            </div>
        @endif

        @if($products->isEmpty())
            <div class="catalog-empty">
                <div class="eyebrow">NO MATCHES</div>
                <h2>პროდუქტი ვერ მოიძებნა</h2>
                <p class="muted">შეცვალე საძიებო სიტყვა ან გაასუფთავე ფილტრები.</p>
                <a class="btn secondary" href="{{ route('catalog') }}">ყველა პროდუქტის ნახვა</a>
            </div>
        @else
            <div class="grid">
                @foreach($products as $product)
                    @include('store.partials.product-card', ['product'=>$product])
                @endforeach
            </div>
            <div class="pagination">{{ $products->links() }}</div>
        @endif
    </div>
</section>
@endsection
