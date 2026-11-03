@extends('layouts.store')
@section('title','შესვლა — VENTOR')
@section('content')
<section class="section"><div class="wrap" style="max-width:560px"><div class="eyebrow">CUSTOMER ACCESS</div><h1>ანგარიშში შესვლა</h1>
@if($errors->any())<div class="notice">{{ $errors->first() }}</div>@endif
<form method="POST" action="{{ route('login.store') }}">@csrf
<label>ელფოსტა</label><input class="field" type="email" name="email" value="{{ old('email') }}" required autofocus>
<label>პაროლი</label><input class="field" type="password" name="password" required>
<label style="display:flex;gap:8px;align-items:center;margin:12px 0"><input type="checkbox" name="remember" value="1"> დამიმახსოვრე</label>
<button class="btn" type="submit">შესვლა</button></form><p class="muted">არ გაქვს ანგარიში? <a href="{{ route('register') }}">რეგისტრაცია</a></p></div></section>
@endsection