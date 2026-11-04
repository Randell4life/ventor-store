<!doctype html>
<html lang="ka">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'VENTOR — საწმენდი და სარეცხი საშუალებების წარმოება')</title>
    <meta name="description" content="@yield('meta_description', 'VENTOR — საავტომობილო, საყოფაცხოვრებო და ინდუსტრიული სარეცხი და საწმენდი საშუალებების წარმოება. ხარისხი და ფასი ერთიან ციკლში.')">
    <meta name="robots" content="@yield('meta_robots', 'index,follow')">
    <meta name="theme-color" content="#080d16">
    <link rel="canonical" href="@yield('canonical_url', url()->current())">
    <meta property="og:locale" content="ka_GE">
    <meta property="og:site_name" content="VENTOR">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="@yield('og_title', trim($__env->yieldContent('title', 'VENTOR — წარმოება და ხარისხი')))">
    <meta property="og:description" content="@yield('meta_description', 'საავტომობილო, საყოფაცხოვრებო და ინდუსტრიული საწმენდი საშუალებების წარმოება.')">
    <meta property="og:url" content="@yield('canonical_url', url()->current())">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', trim($__env->yieldContent('title', 'VENTOR — წარმოება და ხარისხი')))">
    <meta name="twitter:description" content="@yield('meta_description', 'საავტომობილო, საყოფაცხოვრებო და ინდუსტრიული საწმენდი საშუალებების წარმოება.')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Noto+Sans+Georgian:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
    :root{--bg:#070b12;--panel:#0e1724;--panel2:#142238;--line:#263a54;--text:#f4f8ff;--muted:#9aaec7;--blue:#1677ff;--ice:#8fc7ff}
    *{box-sizing:border-box}html{scroll-behavior:smooth;scroll-padding-top:100px}body{margin:0;background:var(--bg);color:var(--text);font-family:Manrope,'Noto Sans Georgian',sans-serif}a{color:inherit;text-decoration:none}img{max-width:100%}.wrap{width:min(1200px,92%);margin:auto}
    header{border-bottom:1px solid var(--line);position:sticky;top:0;background:rgba(7,11,18,.94);backdrop-filter:blur(18px);z-index:50}.nav{min-height:78px;display:flex;align-items:center;justify-content:space-between;gap:24px}.logo{font-size:27px;font-weight:800;letter-spacing:.2em;white-space:nowrap}.logo span{color:var(--blue)}.navlinks{display:flex;gap:clamp(14px,2vw,28px);align-items:center;color:#c2ccda;font-size:13px}.navlinks a{transition:color .2s}.navlinks a:hover{color:var(--ice)}.nav-actions{display:flex;gap:9px;align-items:center;white-space:nowrap}
    .btn{display:inline-flex;align-items:center;justify-content:center;gap:9px;padding:13px 20px;background:linear-gradient(135deg,#1684ff,#075bd0);color:white;border:1px solid transparent;border-radius:4px;font-weight:800;font:inherit;font-size:13px;cursor:pointer;transition:transform .2s,box-shadow .2s}.btn:hover{transform:translateY(-2px);box-shadow:0 12px 28px #006dff45}.btn.secondary{background:transparent;border-color:#38516f;color:#dceaff;box-shadow:none}.eyebrow{color:var(--ice);letter-spacing:.16em;font-size:11px;font-weight:800;text-transform:uppercase}.section{padding:78px 0}.section-head{display:flex;justify-content:space-between;align-items:end;gap:20px;margin-bottom:30px}.section h2{font-size:clamp(26px,4vw,40px);margin:9px 0}.grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px}.card{background:linear-gradient(155deg,#121f30,#0c1420);border:1px solid var(--line);padding:18px;min-width:0}.visual{height:190px;background:radial-gradient(ellipse at 50% 70%,#1c4775,#111e2e 52%,#0b121d);display:grid;place-items:center;margin-bottom:17px;overflow:hidden}.bottle{width:76px;height:130px;border-radius:13px 13px 8px 8px;background:linear-gradient(90deg,#141c28,#34465d,#121a26);border:1px solid #60748c;position:relative;box-shadow:0 20px 35px #0008}.bottle:before{content:'';position:absolute;width:32px;height:24px;background:#202c3b;top:-20px;left:21px;border-radius:4px 4px 0 0}.bottle:after{content:'VENTOR';position:absolute;top:52px;left:0;right:0;text-align:center;font-size:9px;letter-spacing:2px;font-weight:800;color:#b8d9ff}.muted{color:var(--muted)}.price{font-size:20px;font-weight:800;margin:14px 0}.field{width:100%;margin:5px 0 14px;background:#0b1421;color:white;border:1px solid var(--line);padding:13px;border-radius:4px}.field:focus{outline:2px solid #1677ff66;border-color:#398fff}.product-layout{display:grid;grid-template-columns:1fr 1fr;gap:44px}.product-layout .visual{height:440px}.formgrid{display:grid;grid-template-columns:1fr 1fr;gap:12px}.full{grid-column:1/-1}footer{border-top:1px solid var(--line);padding:48px 0 28px;color:var(--muted);font-size:13px;background:#050910}.pagination{padding:20px 0}.notice{border:1px solid #315d8b;padding:14px;color:#c9e4ff;background:#10243a;margin:20px 0}
    @media(max-width:900px){.nav{flex-wrap:wrap;padding:12px 0;gap:12px}.navlinks{order:3;width:100%;overflow-x:auto;white-space:nowrap;padding:4px 0 8px}.grid{grid-template-columns:repeat(2,minmax(0,1fr))}.product-layout{grid-template-columns:1fr}.product-layout .visual{height:300px}}
    @media(max-width:560px){.nav{min-height:0}.logo{font-size:22px}.nav-actions{gap:6px}.nav-actions .btn{padding:10px 11px;font-size:11px}.navlinks{gap:20px}.navlinks a{font-size:11px}.section{padding:54px 0}.grid{gap:10px}.card{padding:10px}.visual{height:150px}.formgrid{grid-template-columns:1fr}.full{grid-column:auto}.product-layout{gap:22px}.product-layout .visual{height:280px}}
    </style>
    <link rel="stylesheet" href="{{ asset('css/storefront-premium.css') }}">
    @stack('head')
</head>
<body>
<header>
    <div class="wrap nav">
        <a class="logo" href="{{ route('home') }}" aria-label="VENTOR — მთავარი">VENTOR<span>.</span></a>
        <nav class="navlinks" aria-label="მთავარი ნავიგაცია">
            <a href="{{ route('home') }}">მთავარი</a>
            <a href="{{ route('catalog') }}">პროდუქცია</a>
            <a href="{{ route('home') }}#production">წარმოება</a>
            <a href="{{ route('home') }}#about">ჩვენ შესახებ</a>
            <a href="{{ route('home') }}#contact">კონტაქტი</a>
        </nav>
        <div class="nav-actions">
            @auth<a class="btn secondary" href="{{ route('account.index') }}">კაბინეტი</a>@else<a class="btn secondary" href="{{ route('login') }}">შესვლა</a>@endauth
            <a class="btn" href="{{ route('cart.index') }}">კალათა ({{ collect(session('cart', []))->sum() }})</a>
        </div>
    </div>
</header>
<main>@yield('content')</main>
<footer id="contact">
    <div class="wrap footer-grid">
        <div><a class="logo" href="{{ route('home') }}">VENTOR<span>.</span></a><p>სისუფთავის ახალი სტანდარტი — წარმოებიდან მომხმარებლამდე.</p></div>
        <div><strong>მიმართულებები</strong><a href="{{ route('catalog') }}">საავტომობილო</a><a href="{{ route('catalog') }}">საყოფაცხოვრებო</a><a href="{{ route('catalog') }}">ინდუსტრიული</a></div>
        <div><strong>VENTOR</strong><a href="{{ route('home') }}#production">წარმოების ციკლი</a><a href="{{ route('home') }}#about">ჩვენ შესახებ</a><a href="{{ route('catalog') }}">პროდუქციის კატალოგი</a></div>
    </div>
    <div class="wrap footer-bottom"><small>© {{ date('Y') }} VENTOR. ყველა უფლება დაცულია.</small><small>ხარისხი, რომელსაც თავად ვქმნით.</small></div>
</footer>
@stack('scripts')
</body>
</html>