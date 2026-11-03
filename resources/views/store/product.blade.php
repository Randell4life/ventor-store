@extends('layouts.store')
@section('title', $product->name.' — VENTOR')
@section('content')
<section class="section product-detail-section"><div class="wrap">
    <div class="breadcrumbs"><a href="{{ route('home') }}">მთავარი</a><span>/</span><a href="{{ route('catalog') }}">კატალოგი</a><span>/</span><span>{{ $product->name }}</span></div>
    <div class="product-detail-grid">
        <div class="product-detail-media">
            <div class="product-media product-detail-image">
                <span class="product-media-index">VENTOR / {{ str_pad((string) $product->id, 3, '0', STR_PAD_LEFT) }}</span>
                @if($product->image_path)<img src="{{ asset('storage/'.$product->image_path) }}" alt="{{ $product->name }}" loading="eager">@else<div class="bottle" aria-hidden="true"></div>@endif
            </div>
            <div class="detail-image-caption"><span>VENTOR SELECTED COLLECTION</span><span>01 / PRODUCT</span></div>
        </div>
        <div class="product-detail-info">
            <div class="eyebrow">{{ $product->category?->name ?? 'VENTOR COLLECTION' }}</div>
            <h1>{{ $product->name }}</h1>
            @if($product->brand || $product->volume)<p class="detail-meta">{{ $product->brand }}@if($product->brand && $product->volume) <span>·</span> @endif{{ $product->volume }}</p>@endif
            @if($product->short_description)<p class="detail-lead">{{ $product->short_description }}</p>@endif
            <div class="detail-price-block"><div class="price detail-price">@if($product->compare_at_price && $product->compare_at_price > $product->price)<span class="old-price">{{ number_format((float)$product->compare_at_price, 2) }} ₾</span>@endif{{ number_format((float)$product->price, 2) }} ₾</div><span class="detail-price-caption">საბოლოო ფასი</span></div>
            <div class="detail-stock">@if($product->stock > 0)<span class="stock-dot"></span> მარაგშია <span class="muted">· {{ $product->stock }} ცალი</span>@else<span class="stock-dot stock-dot-out"></span> დროებით არ არის მარაგში@endif</div>
            @if($product->stock > 0)
                <form class="detail-add-form" method="POST" action="{{ route('cart.add') }}">@csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <label class="quantity-control"><span>რაოდენობა</span><input class="field" type="number" name="quantity" min="1" max="{{ min(30,$product->stock) }}" value="1" required></label>
                    <button class="btn detail-add-button" type="submit">კალათაში დამატება <span>↗</span></button>
                </form>
            @else
                <div class="notice detail-unavailable">პროდუქტი ამჟამად ამოწურულია. გთხოვთ, მოგვიანებით გადაამოწმოთ ხელმისაწვდომობა.</div>
            @endif
            @if($product->sku)<div class="detail-sku">პროდუქტის კოდი <strong>{{ $product->sku }}</strong></div>@endif
            @if($product->description)<div class="detail-description"><div class="eyebrow">PRODUCT DETAILS</div><h2>პროდუქტის აღწერა</h2><p>{{ $product->description }}</p></div>@endif
        </div>
    </div>
</div></section>
@endsection