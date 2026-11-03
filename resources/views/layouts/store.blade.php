<!doctype html>
<html lang="ka">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#070b12">
    <title>@yield('title', 'VENTOR — Premium Automotive Care')</title>
    <meta name="description" content="@yield('meta_description', 'VENTOR — პრემიუმ საავტომობილო და მოტო მოვლის პროდუქტები.')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Noto+Sans+Georgian:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/storefront-premium.css') }}">
    @stack('head')
</head>
<body>
<header class="site-header">
    <div class="wrap nav">
        <a class="logo" href="{{ route('home') }}" aria-label="VENTOR მთავარი">VENTOR<span>.</span></a>
        <nav class="navlinks" aria-label="მთავარი ნავიგაცია">
            <a href="{{ route('home') }}">მთავარი</a>
            <a href="{{ route('catalog') }}">კატალოგი</a>
            <a href="{{ route('catalog', ['category' => 'car-care']) }}">ავტომობილი</a>
            <a href="{{ route('catalog', ['category' => 'moto-care']) }}">მოტო</a>
            <a href="#contact">კონტაქტი</a>
        </nav>
        <a class="btn secondary cart-link" href="{{ route('cart.index') }}"><span>კალათა</span><span class="cart-count">{{ collect(session('cart', []))->sum() }}</span></a>
    </div>
</header>
@if(session('success') && !request()->routeIs('checkout.success'))
    <div class="wrap"><div class="notice" role="status">{{ session('success') }}</div></div>
@endif
<main>@yield('content')</main>
<footer id="contact" class="site-footer">
    <div class="wrap footer-grid">
        <div><a class="logo" href="{{ route('home') }}">VENTOR<span>.</span></a><p class="footer-tagline">PREMIUM AUTOMOTIVE CARE</p></div>
        <div><div class="eyebrow">VENTOR STANDARD</div><p>ავტომობილისა და მოტოციკლის მოვლის გააზრებული არჩევანი. ხარისხი ყველა დეტალში.</p></div>
        <div><div class="eyebrow">ნავიგაცია</div><a href="{{ route('catalog') }}">პროდუქციის კატალოგი</a><a href="{{ route('cart.index') }}">კალათა</a></div>
    </div>
    <div class="wrap footer-bottom"><span>© {{ date('Y') }} VENTOR. ყველა უფლება დაცულია.</span><span>MADE FOR THOSE WHO CARE.</span></div>
</footer>
@stack('scripts')
</body>
</html>