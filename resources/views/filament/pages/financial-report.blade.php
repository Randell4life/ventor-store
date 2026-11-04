<x-filament-panels::page>
    <div class="space-y-6">
        <form method="GET" class="flex flex-wrap items-end gap-3 rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div>
                <label for="from" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">პერიოდი — დან</label>
                <input id="from" name="from" type="date" value="{{ $from }}" class="rounded-lg border-gray-300 text-sm dark:border-gray-700 dark:bg-gray-950">
            </div>
            <div>
                <label for="until" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">პერიოდი — მდე</label>
                <input id="until" name="until" type="date" value="{{ $until }}" class="rounded-lg border-gray-300 text-sm dark:border-gray-700 dark:bg-gray-950">
            </div>
            <button type="submit" class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-500">ანგარიშის ჩვენება</button>
            <a href="{{ request()->url() }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 dark:border-gray-700 dark:text-gray-200">მიმდინარე თვე</a>
        </form>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                ['დასრულებული შეკვეთები', number_format($orderCount).''],
                ['გაყიდვები', number_format($revenue, 2).' ₾'],
                ['თვითღირებულება', number_format($cost, 2).' ₾'],
                ['მთლიანი მოგება', number_format($grossProfit, 2).' ₾'],
            ] as [$label, $value])
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $label }}</div>
                    <div class="mt-2 text-2xl font-bold tracking-tight text-gray-950 dark:text-white">{{ $value }}</div>
                </div>
            @endforeach
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <div class="text-sm text-gray-500 dark:text-gray-400">დაფიქსირებული ფასდაკლებები</div>
                <div class="mt-2 text-xl font-semibold text-gray-950 dark:text-white">{{ number_format($discounts, 2) }} ₾</div>
            </div>
            <div class="rounded-xl border border-amber-300 bg-amber-50 p-5 dark:border-amber-800 dark:bg-amber-950/30">
                <div class="text-sm text-amber-800 dark:text-amber-300">თვითღირებულება არასრულია</div>
                <div class="mt-2 text-xl font-semibold text-amber-950 dark:text-amber-100">{{ $incompleteCount }} შეკვეთა</div>
                <p class="mt-1 text-xs text-amber-800 dark:text-amber-300">ამ შეკვეთების თანხები მოგების ანგარიშში არ შედის.</p>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800">
                <h2 class="font-semibold text-gray-950 dark:text-white">დასრულებული შეკვეთები</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $from }} — {{ $until }}</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-gray-600 dark:bg-gray-950 dark:text-gray-300">
                        <tr>
                            <th class="px-5 py-3 font-medium">შეკვეთა</th>
                            <th class="px-5 py-3 font-medium">მომხმარებელი</th>
                            <th class="px-5 py-3 text-right font-medium">გაყიდვა</th>
                            <th class="px-5 py-3 text-right font-medium">თვითღირებულება</th>
                            <th class="px-5 py-3 text-right font-medium">მთლიანი მოგება</th>
                            <th class="px-5 py-3 text-right font-medium">დასრულდა</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($orders as $order)
                            @php
                                $orderCost = (float) $order->items->sum(fn ($item) => (float) $item->unit_cost * (int) $item->quantity);
                                $profit = (float) $order->total - $orderCost;
                            @endphp
                            <tr>
                                <td class="whitespace-nowrap px-5 py-3 font-semibold text-gray-950 dark:text-white">{{ $order->order_number }}</td>
                                <td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $order->customer_name }}</td>
                                <td class="whitespace-nowrap px-5 py-3 text-right">{{ number_format((float) $order->total, 2) }} ₾</td>
                                <td class="whitespace-nowrap px-5 py-3 text-right">{{ number_format($orderCost, 2) }} ₾</td>
                                <td class="whitespace-nowrap px-5 py-3 text-right font-semibold {{ $profit < 0 ? 'text-danger-600' : 'text-success-600' }}">{{ number_format($profit, 2) }} ₾</td>
                                <td class="whitespace-nowrap px-5 py-3 text-right text-gray-500">{{ $order->updated_at?->format('d.m.Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-10 text-center text-gray-500">არჩეულ პერიოდში აღრიცხული დასრულებული შეკვეთები არ მოიძებნა.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <p class="text-xs text-gray-500 dark:text-gray-400">მთლიანი მოგება = შეკვეთის საბოლოო თანხა − გაყიდვისას დაფიქსირებული პროდუქტის თვითღირებულება. საოპერაციო ხარჯები, გადასახადები და გადახდის საკომისიოები არ არის გამოკლებული. პერიოდი ეფუძნება შეკვეთის ბოლო განახლების თარიღს (სტატუსის დასრულების თარიღის შემცვლელი).</p>
    </div>
</x-filament-panels::page>
