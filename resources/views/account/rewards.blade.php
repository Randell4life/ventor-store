@extends('layouts.store')
@section('title', 'VENTOR Rewards — ქულების ისტორია')
@section('content')
<section class="section">
    <div class="wrap">
        <div class="eyebrow">VENTOR REWARDS</div>
        <h1>ქულების ისტორია</h1>
        <div class="card" style="display:flex;justify-content:space-between;align-items:center;gap:20px;flex-wrap:wrap;margin:24px 0;padding:22px">
            <div><div class="eyebrow">მიმდინარე ბალანსი</div><p class="muted" style="margin:6px 0 0">შენი ხელმისაწვდომი ბონუს ქულები</p></div>
            <strong style="font-size:32px">{{ number_format((int) auth()->user()->loyalty_points) }} <span style="font-size:15px">ქულა</span></strong>
        </div>
        @if($transactions->isEmpty())
            <div class="card">
                <h2>ისტორია ჯერ ცარიელია</h2>
                <p class="muted">შეკვეთების დასრულების შემდეგ აქ გამოჩნდება დარიცხული და გამოყენებული ქულები.</p>
                <a class="btn" href="{{ route('catalog') }}">პროდუქციის ნახვა</a>
            </div>
        @else
            <div class="card">
                @foreach($transactions as $transaction)
                    <div style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;padding:16px 0;border-bottom:1px solid var(--line)">
                        <div>
                            <strong>{{ $transaction->description ?: ($transaction->type === 'earned' ? 'ქულების დარიცხვა' : 'ქულების გამოყენება') }}</strong>
                            <p class="muted" style="margin:5px 0 0">{{ $transaction->created_at->format('d.m.Y H:i') }}@if($transaction->order) · შეკვეთა {{ $transaction->order->order_number }}@endif</p>
                            <small class="muted">ბალანსი ოპერაციის შემდეგ: {{ number_format((int) $transaction->balance_after) }} ქულა</small>
                        </div>
                        <strong style="white-space:nowrap">{{ $transaction->type === 'earned' ? '+' : '−' }}{{ number_format((int) $transaction->points) }} ქ.</strong>
                    </div>
                @endforeach
            </div>
            <div class="pagination">{{ $transactions->links() }}</div>
        @endif
        <p style="margin-top:24px"><a href="{{ route('account.index') }}">← პირად კაბინეტში დაბრუნება</a></p>
    </div>
</section>
@endsection
