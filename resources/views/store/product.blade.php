@extends('layouts.store')
@push('head')<link rel="stylesheet" href="{{ asset('css/storefront-premium.css') }}">@endpush
@section('title',$product->name.' — VENTOR')
@section('content')
<section class="section">
    <div class="wrap">
        <div class="muted" style="font-size:12px;margin-bottom:22px">
            <a href="{{ route('home') }}">მთავარი</a> / <a href="{{ route('catalog') }}">კატალოგი</a> / {{ $product->name }}
        </div>
        <div class="product-layout">
            <div class="visual" style="min-height:420px;border:1px solid var(--line)">
                @if($product->image_path)
                    <img src="{{ asset('storage/'.$product->image_path) }}" alt="{{ $product->name }}" loading="eager" style="width:100%;height:100%;max-height:520px;object-fit:contain">
                @else
                    <div class="bottle" style="transform:scale(1.7)" aria-hidden="true"></div>
                @endif
            </div>
            <div>
                <div class="eyebrow">{{ $product->category?->name ?? 'VENTOR COLLECTION' }}</div>
                <h1 style="font-size:clamp(34px,4.5vw,52px);margin:16px 0">{{ $product->name }}</h1>
                @if($product->brand)<p class="muted">{{ $product->brand }} @if($product->volume) · {{ $product->volume }} @endif</p>@endif
                @if($product->short_description)<p class="muted" style="line-height:1.8">{{ $product->short_description }}</p>@endif
                <div class="price" style="font-size:30px;margin:22px 0">
                    @if($product->compare_at_price && $product->compare_at_price > $product->price)
                        <span class="muted" style="font-size:16px;text-decoration:line-through;margin-right:8px">{{ number_format((float)$product->compare_at_price,2) }} ₾</span>
                    @endif
                    {{ number_format((float)$product->price,2) }} ₾
                </div>
                @if($product->stock > 0)
                    <p style="color:#8bd4a4;font-size:12px">● მარაგშია — {{ $product->stock }} ცალი</p>
                    <form method="POST" action="{{ route('cart.add') }}" style="display:flex;gap:12px;align-items:end;flex-wrap:wrap;margin:22px 0">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div><label for="quantity" class="muted" style="font-size:12px">რაოდენობა</label><input id="quantity" class="field" style="width:110px" type="number" name="quantity" min="1" max="{{ min(30,$product->stock) }}" value="1" required></div>
                        <button class="btn" type="submit">კალათაში დამატება</button>
                    </form>
                @else
                    <p class="notice">პროდუქტი ამჟამად არ არის მარაგში.</p>
                @endif
                @if($product->sku)<p class="muted" style="font-size:11px">SKU: {{ $product->sku }}</p>@endif
                @if($product->description)
                    <div style="border-top:1px solid var(--line);padding-top:20px;margin-top:24px">
                        <h3>პროდუქტის აღწერა</h3><p style="line-height:1.9;color:#c2cfdf;white-space:pre-line">{{ $product->description }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
