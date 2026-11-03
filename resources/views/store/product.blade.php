@extends('layouts.store')
@push('head')
<link rel="stylesheet" href="{{ asset('css/storefront-premium.css') }}">
<style>
.product-detail{display:grid;grid-template-columns:minmax(0,1.05fr) minmax(0,.95fr);gap:clamp(24px,5vw,64px);align-items:start}
.product-gallery{position:sticky;top:110px}
.product-gallery .visual{height:clamp(340px,42vw,540px);margin:0;border:1px solid var(--line)}
.product-info h1{font-size:clamp(34px,4.5vw,54px);line-height:1.08;letter-spacing:-.04em}
.product-kicker{display:flex;gap:10px;align-items:center;flex-wrap:wrap}
.product-badge{border:1px solid #315d8b;color:var(--ice);font-size:11px;padding:6px 9px}
.product-price{font-size:34px;font-weight:800;letter-spacing:-.03em}
.product-stock{display:flex;gap:9px;align-items:center;padding:12px 14px;background:#0d1d1b;border:1px solid #234c40;color:#9be2bd;font-size:13px}
.product-stock.out{background:#251616;border-color:#613333;color:#ffb2b2}
.product-buy{display:flex;gap:10px;align-items:end;flex-wrap:wrap;margin:20px 0}
.product-buy input{width:105px;min-height:46px}
.product-description{border-top:1px solid var(--line);padding-top:22px;margin-top:25px}
.product-facts{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;margin:20px 0}
.product-fact{padding:13px;background:var(--panel);border:1px solid var(--line)}
.product-fact small{display:block;color:var(--muted);font-size:10px;margin-bottom:5px}
.product-fact strong{font-size:13px}
.related-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:15px}
@media(max-width:850px){.product-detail{grid-template-columns:1fr}.product-gallery{position:relative;top:auto}.product-gallery .visual{height:350px}.related-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:560px){.product-gallery .visual{height:280px}.product-buy .btn{flex:1}.product-facts{gap:7px}.product-fact{padding:10px}}
</style>
@endpush
@section('title',$product->name.' — VENTOR')
@section('content')
<section class="section">
    <div class="wrap">
        <nav class="muted" aria-label="Breadcrumb" style="font-size:12px;margin-bottom:24px">
            <a href="{{ route('home') }}">მთავარი</a> / <a href="{{ route('catalog') }}">კატალოგი</a>
            @if($product->category) / <a href="{{ route('catalog',['category'=>$product->category->slug]) }}">{{ $product->category->name }}</a>@endif
            / <span style="color:var(--text)">{{ $product->name }}</span>
        </nav>

        <div class="product-detail">
            <div class="product-gallery">
                <div class="visual">
                    @if($product->image_path)
                        <img src="{{ asset('storage/'.$product->image_path) }}" alt="{{ $product->name }}" loading="eager" style="width:100%;height:100%;object-fit:contain">
                    @else
                        <div class="bottle" style="transform:scale(1.7)" aria-hidden="true"></div>
                    @endif
                </div>
            </div>

            <div class="product-info">
                <div class="product-kicker">
                    <span class="eyebrow">{{ $product->category?->name ?? 'VENTOR COLLECTION' }}</span>
                    @if($product->is_featured)<span class="product-badge">რჩეული პროდუქტი</span>@endif
                </div>
                <h1 style="margin:15px 0">{{ $product->name }}</h1>
                @if($product->brand || $product->volume)
                    <p class="muted" style="margin:0 0 18px">{{ $product->brand }} @if($product->volume) · {{ $product->volume }} @endif</p>
                @endif
                @if($product->short_description)
                    <p class="muted" style="line-height:1.9;font-size:14px">{{ $product->short_description }}</p>
                @endif

                <div style="margin:22px 0 16px">
                    @if($product->compare_at_price && $product->compare_at_price > $product->price)
                        <div class="muted" style="font-size:14px;text-decoration:line-through">{{ number_format((float)$product->compare_at_price,2) }} ₾</div>
                    @endif
                    <div class="product-price">{{ number_format((float)$product->price,2) }} ₾</div>
                </div>

                @if($product->stock > 0)
                    <div class="product-stock">● მარაგშია <strong>{{ $product->stock }}</strong> ცალი</div>
                    <form method="POST" action="{{ route('cart.add') }}" class="product-buy">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div>
                            <label for="quantity" class="muted" style="font-size:11px">რაოდენობა</label>
                            <input id="quantity" class="field" type="number" name="quantity" min="1" max="{{ min(30,$product->stock) }}" value="1" required>
                        </div>
                        <button class="btn" type="submit">კალათაში დამატება</button>
                    </form>
                @else
                    <div class="product-stock out">● პროდუქტი დროებით არ არის მარაგში</div>
                @endif

                <div class="product-facts">
                    @if($product->sku)<div class="product-fact"><small>პროდუქტის კოდი</small><strong>{{ $product->sku }}</strong></div>@endif
                    @if($product->brand)<div class="product-fact"><small>ბრენდი</small><strong>{{ $product->brand }}</strong></div>@endif
                    @if($product->volume)<div class="product-fact"><small>მოცულობა</small><strong>{{ $product->volume }}</strong></div>@endif
                    <div class="product-fact"><small>ხელმისაწვდომობა</small><strong>{{ $product->stock > 0 ? 'მარაგშია' : 'არ არის მარაგში' }}</strong></div>
                </div>

                @if($product->description)
                    <div class="product-description">
                        <h2 style="font-size:20px">პროდუქტის აღწერა</h2>
                        <p class="muted" style="line-height:2;white-space:pre-line">{{ $product->description }}</p>
                    </div>
                @endif
                <p class="muted" style="font-size:12px;margin-top:22px">VENTOR · Premium Automotive Care</p>
            </div>
        </div>
    </div>
</section>

@if(isset($relatedProducts) && $relatedProducts->isNotEmpty())
<section class="section" style="padding-top:10px">
    <div class="wrap">
        <div class="section-head"><div><div class="eyebrow">YOU MAY ALSO LIKE</div><h2>მსგავსი პროდუქტები</h2></div><a href="{{ route('catalog') }}">კატალოგი →</a></div>
        <div class="related-grid">
            @foreach($relatedProducts as $relatedProduct)
                @include('store.partials.product-card',['product'=>$relatedProduct])
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
