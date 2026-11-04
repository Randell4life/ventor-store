@extends('layouts.store')

@section('title', 'VENTOR — ავტომობილის მოვლის პრემიუმ საშუალებები')
@section('meta_description', 'VENTOR — ავტომობილის მოვლის, რეცხვისა და დეტეილინგის საშუალებები. აღმოაჩინე პროფესიონალური ავტოქიმია, საყოფაცხოვრებო და ინდუსტრიული პროდუქტები.')
@section('canonical_url', route('home'))

@section('content')
<div class="store-promo-bar">
    <div class="wrap"><span>VENTOR PROFESSIONAL CARE</span><span>ხარისხი, რომელსაც შენი ავტომობილი იმსახურებს</span><a href="{{ route('catalog') }}">შეარჩიე პროდუქცია <b>↗</b></a></div>
</div>

<section class="store-hero">
    <div class="store-hero-photo" aria-hidden="true"></div>
    <div class="store-hero-overlay" aria-hidden="true"></div>
    <div class="wrap store-hero-content">
        <div class="store-hero-copy">
            <div class="store-kicker"><span></span> AUTOMOTIVE CARE · MADE WITH PRECISION</div>
            <h1>შენი ავტომობილი.<br><em>შენი სტანდარტი.</em></h1>
            <p>პროფესიონალური მოვლა იწყება სწორი პროდუქტით. აღმოაჩინე VENTOR-ის საავტომობილო საწმენდი და მოვლის საშუალებები ყოველდღიური შედეგისთვის.</p>
            <div class="store-hero-actions">
                <a class="btn store-cta" href="{{ route('catalog') }}">შეარჩიე პროდუქცია <span>↗</span></a>
                <a class="store-quiet-link" href="#ventor-categories">კატეგორიების ნახვა <span>↓</span></a>
            </div>
            <div class="store-hero-metrics"><span><b>01</b> ეფექტური ფორმულები</span><span><b>02</b> კონტროლირებული წარმოება</span><span><b>03</b> ყოველდღიური მოვლა</span></div>
        </div>
        <div class="store-hero-stamp"><span>VENTOR</span><small>CARE THAT<br>MAKES A DIFFERENCE</small></div>
    </div>
    <div class="store-hero-caption"><span>ENGINEERED FOR A CLEANER DRIVE</span><span>VENTOR / GEORGIA</span></div>
</section>

<section class="store-benefits">
    <div class="wrap">
        <div><span class="store-benefit-icon">✦</span><span><b>პროფესიონალური ხარისხი</b><small>ყურადღებით შექმნილი ფორმულები</small></span></div>
        <div><span class="store-benefit-icon">◈</span><span><b>სრული საწარმოო ციკლი</b><small>ნედლეულიდან მზა პროდუქტამდე</small></span></div>
        <div><span class="store-benefit-icon">↗</span><span><b>ფასი და ხარისხი</b><small>გონივრული არჩევანი ყოველდღე</small></span></div>
    </div>
</section>

<section class="section ventor-directions" id="ventor-categories">
    <div class="wrap">
        <div class="ventor-directions-heading">
            <div>
                <div class="eyebrow">01 / THE VENTOR WORLD</div>
                <h2>სისუფთავის <em>ახალი სტანდარტი.</em></h2>
            </div>
            <p>აღმოაჩინე VENTOR-ის პროდუქციის კატეგორიები და შეარჩიე შენთვის საჭირო მიმართულება.</p>
        </div>

        @if($categories->isNotEmpty())
            <div class="ventor-categories-grid">
                @foreach($categories as $category)
                    <a class="ventor-category-card" href="{{ route('catalog', ['category' => $category->slug]) }}">
                        <span class="ventor-category-image" @if($category->image_path) style="background-image: url('{{ asset('storage/' . $category->image_path) }}')" @endif aria-hidden="true"></span>
                        <span class="ventor-category-shade" aria-hidden="true"></span>
                        <span class="ventor-category-top"><b>{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</b><span>VENTOR COLLECTION</span></span>
                        <span class="ventor-category-content">
                            <strong>{{ $category->name }}</strong>
                            @if($category->description)
                                <span>{{ $category->description }}</span>
                            @endif
                            <i>კატეგორიის ნახვა <b>↗</b></i>
                        </span>
                    </a>
                @endforeach
            </div>
        @else
            <div class="ventor-categories-empty">
                <strong>კატეგორიები ჯერ არ დამატებულა</strong>
                <span>დაამატე კატეგორიები ადმინისტრირების პანელიდან და ისინი აქ ავტომატურად გამოჩნდება.</span>
            </div>
        @endif
        @if($categories->isNotEmpty())
            <div class="store-category-chips">
                <span>კატეგორიები:</span>
                @foreach($categories as $category)
                    <a href="{{ route('catalog', ['category' => $category->slug]) }}">{{ $category->name }} <b>↗</b></a>
                @endforeach
            </div>
        @endif
    </div>
</section>

<section class="section store-featured-products" id="featured-products">
    <div class="wrap">
        @php
            $homeProducts = $featured->isNotEmpty() ? $featured : $products;
            $showingFeatured = $featured->isNotEmpty();
        @endphp
        <div class="store-section-heading">
            <div><div class="eyebrow">02 / THE VENTOR SELECTION</div><h2>{{ $showingFeatured ? 'რჩეული პროდუქტები' : 'უახლესი პროდუქტები' }}</h2><p class="store-heading-note">შეარჩიე პროდუქტი შენი ავტომობილისა და ყოველდღიური საჭიროებებისთვის.</p></div>
            <a class="store-view-all" href="{{ route('catalog') }}">ყველა პროდუქტი <span>↗</span></a>
        </div>
        @if($homeProducts->isNotEmpty())
            <div class="grid product-grid store-product-grid">
                @foreach($homeProducts as $product)
                    @include('store.partials.product-card', ['product' => $product])
                @endforeach
            </div>
        @else
            <div class="empty-products"><div class="eyebrow">VENTOR PRODUCT RANGE</div><h3>პროდუქციის კატალოგი მალე შეივსება.</h3><p>გამოქვეყნებული პროდუქტები ამ სივრცეში ავტომატურად გამოჩნდება.</p><a class="btn secondary" href="{{ route('catalog') }}">კატალოგის ნახვა ↗</a></div>
        @endif
    </div>
</section>

<section class="store-search-band">
    <div class="wrap store-search-panel">
        <div><div class="eyebrow">FIND YOUR PRODUCT</div><h2>ეძებ კონკრეტულ საშუალებას?</h2><p>მოძებნე პროდუქტის სახელით, ბრენდით ან კოდით.</p></div>
        <form method="GET" action="{{ route('catalog') }}" class="store-search-form">
            <label class="sr-only" for="ventor-product-search">პროდუქტის ძიება</label>
            <input id="ventor-product-search" type="search" name="q" placeholder="მაგ. სალონის საწმენდი..." value="{{ request('q') }}">
            <button class="btn" type="submit">ძიება <span>↗</span></button>
        </form>
    </div>
</section>

<section class="store-production" id="production">
    <div class="wrap store-production-layout">
        <div id="about"><h2>შედეგი იწყება<br><em>წარმოების კონტროლით.</em></h2><p>VENTOR აერთიანებს ნედლეულის შერჩევას, ფორმულირებას, ტარის წარმოებასა და შეფუთვას. სრული ციკლის კონტროლი გვაძლევს საშუალებას, შევქმნათ საიმედო პროდუქტი კონკურენტული ფასით.</p><a class="store-quiet-link" href="{{ route('catalog') }}">აღმოაჩინე VENTOR <span>↗</span></a></div>
        <div class="store-production-list">
            <div><b>01</b><span><strong>ნედლეულის შერჩევა</strong><small>ხარისხიანი კომპონენტების მომარაგება</small></span></div>
            <div><b>02</b><span><strong>ფორმულირება და სინთეზი</strong><small>პროდუქტის ეფექტურობის შექმნა</small></span></div>
            <div><b>03</b><span><strong>ტარის წარმოება</strong><small>პროდუქტზე მორგებული შეფუთვა</small></span></div>
            <div><b>04</b><span><strong>ჩამოსხმა და ხარისხის კონტროლი</strong><small>მზა პროდუქტის შემოწმება და შეფუთვა</small></span></div>
        </div>
    </div>
</section>
@endsection
