@extends('layouts.store')
@section('title','პროფილი — VENTOR')
@section('content')
<section class="section"><div class="wrap" style="max-width:760px"><div class="eyebrow">ACCOUNT SETTINGS</div><h1>პროფილის მართვა</h1>
@if(session('status'))<div class="notice">{{ session('status') }}</div>@endif
@if($errors->any())<div class="notice">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
<form method="POST" action="{{ route('account.profile.update') }}">@csrf @method('PUT')
<label>სახელი და გვარი</label><input class="field" name="name" value="{{ old('name',$user->name) }}" required>
<label>ელფოსტა</label><input class="field" type="email" name="email" value="{{ old('email',$user->email) }}" required>
<label>ტელეფონი</label><input class="field" name="phone" value="{{ old('phone',$user->phone) }}">
<label>კომპანიის დასახელება (საბითუმო მომხმარებლისთვის)</label><input class="field" name="company_name" value="{{ old('company_name',$user->company_name) }}">
<label>საიდენტიფიკაციო / საგადასახადო ნომერი</label><input class="field" name="tax_number" value="{{ old('tax_number',$user->tax_number) }}">
<button class="btn" type="submit">ცვლილებების შენახვა</button> <a class="btn secondary" href="{{ route('account.index') }}">უკან</a></form></div></section>
@endsection