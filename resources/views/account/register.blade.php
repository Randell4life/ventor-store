@extends('layouts.store')
@section('title','ანგარიშის შექმნა — VENTOR')
@section('content')
<section class="section"><div class="wrap" style="max-width:620px"><div class="eyebrow">JOIN VENTOR</div><h1>ანგარიშის შექმნა</h1>
@if($errors->any())<div class="notice">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
<form method="POST" action="{{ route('register.store') }}">@csrf
<label>სახელი და გვარი</label><input class="field" name="name" value="{{ old('name') }}" required maxlength="120">
<label>ელფოსტა</label><input class="field" type="email" name="email" value="{{ old('email') }}" required>
<label>ტელეფონი</label><input class="field" name="phone" value="{{ old('phone') }}">
<label>პაროლი (მინ. 10 სიმბოლო)</label><input class="field" type="password" name="password" required minlength="10">
<label>გაიმეორე პაროლი</label><input class="field" type="password" name="password_confirmation" required minlength="10">
<button class="btn" type="submit">ანგარიშის შექმნა</button></form><p class="muted">უკვე გაქვს ანგარიში? <a href="{{ route('login') }}">შესვლა</a></p></div></section>
@endsection