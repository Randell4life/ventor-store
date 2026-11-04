<div class="space-y-3 p-4">
    @forelse ($payments as $payment)
        <div class="flex flex-wrap items-start justify-between gap-3 rounded-lg border border-gray-200 p-3 dark:border-gray-700">
            <div>
                <div class="font-semibold text-gray-950 dark:text-white">{{ number_format((float) $payment->amount, 2) }} ₾</div>
                <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ match ($payment->payment_method) {
                        'bank_transfer' => 'საბანკო გადარიცხვა',
                        'cash' => 'ნაღდი ფული',
                        'card' => 'ბარათი',
                        default => 'სხვა',
                    } }}
                    · {{ $payment->paid_at?->format('d.m.Y H:i') }}
                </div>
                @if ($payment->reference)
                    <div class="mt-1 text-xs text-gray-500">რეფერენსი: {{ $payment->reference }}</div>
                @endif
                @if ($payment->note)
                    <div class="mt-1 text-sm text-gray-600 dark:text-gray-300">{{ $payment->note }}</div>
                @endif
            </div>
            <div class="text-xs text-gray-500">{{ $payment->user?->name ?? 'სისტემა' }}</div>
        </div>
    @empty
        <p class="text-sm text-gray-500">გადახდები ჯერ არ არის დაფიქსირებული.</p>
    @endforelse
</div>
