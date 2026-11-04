<x-filament-panels::page>
    <style>
        .fr-dashboard{--fr-bg:#f4f7fc;--fr-card:#fff;--fr-line:#e4eaf3;--fr-text:#152238;--fr-muted:#718096;--fr-blue:#2563eb;--fr-blue-soft:#eaf1ff;--fr-green:#079669;--fr-amber:#c47a0a;--fr-red:#dc4b55;color:var(--fr-text);display:grid;gap:24px}
        .dark .fr-dashboard{--fr-bg:#0c1422;--fr-card:#121e30;--fr-line:#26364d;--fr-text:#edf4ff;--fr-muted:#9aabc2;--fr-blue:#60a5fa;--fr-blue-soft:#172d4c;--fr-green:#34d399;--fr-amber:#fbbf24;--fr-red:#fb7185}
        .fr-hero{background:linear-gradient(120deg,#101d33 0%,#173b68 58%,#1764b7 100%);border-radius:22px;padding:26px 28px;color:#fff;display:flex;align-items:center;justify-content:space-between;gap:20px;overflow:hidden;position:relative}
        .fr-hero:after{content:"";position:absolute;width:260px;height:260px;border:1px solid #ffffff20;border-radius:50%;right:8%;top:-155px;box-shadow:0 0 0 35px #ffffff08,0 0 0 70px #ffffff06}
        .fr-eyebrow{font-size:11px;letter-spacing:.14em;text-transform:uppercase;color:#b9d5fa;font-weight:800}
        .fr-hero h2{font-size:clamp(23px,3vw,32px);font-weight:850;letter-spacing:-.035em;margin:7px 0 4px}
        .fr-hero p{font-size:13px;color:#d1e2f8;margin:0}
        .fr-period{display:flex;flex-wrap:wrap;align-items:end;gap:10px;z-index:1}
        .fr-period label{display:grid;gap:5px;color:#dceaff;font-size:11px;font-weight:700}
        .fr-period input{background:#ffffff14;border:1px solid #ffffff45;color:#fff;border-radius:9px;padding:9px 11px;min-height:40px;color-scheme:dark}
        .fr-period button,.fr-period a{border:1px solid #ffffff45;border-radius:9px;padding:10px 14px;font-size:12px;font-weight:800;color:#fff;text-decoration:none;min-height:40px}
        .fr-period button{background:#fff;color:#17477f;border-color:#fff;cursor:pointer}
        .fr-section{display:grid;gap:13px}
        .fr-section-head{display:flex;justify-content:space-between;align-items:end;gap:12px}
        .fr-section-head h3{font-size:17px;font-weight:850;letter-spacing:-.02em;margin:0;color:var(--fr-text)}
        .fr-section-head p{font-size:12px;color:var(--fr-muted);margin:4px 0 0}
        .fr-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:13px}
        .fr-card{background:var(--fr-card);border:1px solid var(--fr-line);border-radius:15px;padding:17px;min-width:0;box-shadow:0 4px 14px #172b4d08}
        .fr-card-top{display:flex;justify-content:space-between;align-items:center;gap:8px}
        .fr-card-label{font-size:11px;font-weight:750;color:var(--fr-muted);line-height:1.45}
        .fr-icon{width:34px;height:34px;border-radius:10px;display:grid;place-items:center;background:var(--fr-blue-soft);color:var(--fr-blue);font-size:17px;flex:none}
        .fr-value{font-size:clamp(20px,2vw,27px);font-weight:900;letter-spacing:-.045em;line-height:1.2;margin-top:12px;color:var(--fr-text);overflow-wrap:anywhere}
        .fr-foot{font-size:11px;color:var(--fr-muted);margin-top:7px}
        .fr-value.green{color:var(--fr-green)}.fr-value.amber{color:var(--fr-amber)}.fr-value.red{color:var(--fr-red)}
        .fr-panel{background:var(--fr-card);border:1px solid var(--fr-line);border-radius:16px;overflow:hidden;box-shadow:0 4px 14px #172b4d08}
        .fr-panel-head{padding:16px 19px;border-bottom:1px solid var(--fr-line);display:flex;justify-content:space-between;align-items:center;gap:10px}
        .fr-panel-head strong{font-size:13px;font-weight:850;color:var(--fr-text)}
        .fr-panel-head span{font-size:11px;color:var(--fr-muted)}
        .fr-table-wrap{overflow-x:auto}
        .fr-table{width:100%;border-collapse:collapse;font-size:12px;min-width:620px}
        .fr-table th{background:var(--fr-bg);color:var(--fr-muted);font-size:10px;text-transform:uppercase;letter-spacing:.06em;font-weight:800;padding:12px 15px;text-align:left;white-space:nowrap}
        .fr-table td{padding:13px 15px;border-top:1px solid var(--fr-line);color:var(--fr-text);vertical-align:middle}
        .fr-table .num{text-align:right;font-variant-numeric:tabular-nums;white-space:nowrap}
        .fr-muted{color:var(--fr-muted)!important}
        .fr-pill{display:inline-flex;align-items:center;border-radius:99px;padding:4px 8px;font-size:10px;font-weight:800;background:var(--fr-blue-soft);color:var(--fr-blue)}
        .fr-pill.paid{background:#e7f8f1;color:#087d58}.fr-pill.unpaid{background:#fff4df;color:#a76500}
        .dark .fr-pill.paid{background:#103b32;color:#6ee7b7}.dark .fr-pill.unpaid{background:#49351a;color:#fcd34d}
        .fr-warning{border:1px solid #f2cf83;background:#fff8e9;color:#85520b;border-radius:13px;padding:13px 16px;font-size:12px}
        .dark .fr-warning{background:#392b18;border-color:#76551f;color:#ffdf9b}
        .fr-split{display:grid;grid-template-columns:1.1fr .9fr;gap:14px}
        .fr-bars{padding:17px 19px;display:grid;gap:14px}
        .fr-bar-row{display:grid;grid-template-columns:minmax(100px,1fr) 2fr auto;align-items:center;gap:12px;font-size:11px}
        .fr-bar-name{font-weight:750;color:var(--fr-text);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
        .fr-track{height:8px;background:var(--fr-bg);border-radius:99px;overflow:hidden}
        .fr-fill{height:100%;border-radius:99px;background:linear-gradient(90deg,#2563eb,#48a5ff)}
        .fr-bar-amount{font-weight:800;color:var(--fr-text);font-variant-numeric:tabular-nums;white-space:nowrap}
        .fr-chart{padding:18px 18px 14px;display:flex;align-items:end;gap:9px;height:185px}
        .fr-chart-item{height:100%;flex:1;display:flex;flex-direction:column;justify-content:end;align-items:center;gap:7px;min-width:0}
        .fr-chart-value{font-size:9px;color:var(--fr-muted);white-space:nowrap}
        .fr-chart-bar{width:min(100%,30px);min-height:3px;border-radius:6px 6px 2px 2px;background:linear-gradient(180deg,#60a5fa,#2563eb)}
        .fr-chart-date{font-size:9px;color:var(--fr-muted);white-space:nowrap}
        .fr-note{font-size:11px;line-height:1.6;color:var(--fr-muted)}
        .fr-empty{padding:28px;text-align:center;color:var(--fr-muted);font-size:12px}
        @media(max-width:1100px){.fr-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.fr-split{grid-template-columns:1fr}}
        @media(max-width:640px){.fr-dashboard{gap:17px}.fr-hero{padding:20px;display:grid}.fr-period{display:grid;grid-template-columns:1fr 1fr}.fr-period label input{width:100%;min-width:0}.fr-period button,.fr-period a{display:flex;align-items:center;justify-content:center}.fr-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:9px}.fr-card{padding:13px}.fr-value{font-size:19px}.fr-bar-row{grid-template-columns:90px 1fr auto;gap:7px}.fr-chart{gap:4px;padding-inline:9px}.fr-chart-value{font-size:8px}}
    </style>

    <div class="fr-dashboard">
        <header class="fr-hero">
            <div>
                <div class="fr-eyebrow">VENTOR · BUSINESS INTELLIGENCE</div>
                <h2>ფინანსური მიმოხილვა</h2>
                <p>გაყიდვები, ხარჯები და საწყობის ღირებულება ერთ სივრცეში</p>
            </div>
            <form method="GET" class="fr-period">
                <label>პერიოდი — დან<input name="from" type="date" value="{{ $from }}"></label>
                <label>პერიოდი — მდე<input name="until" type="date" value="{{ $until }}"></label>
                <button type="submit">განახლება</button>
                <a href="{{ request()->url() }}">მიმდინარე თვე</a>
            </form>
        </header>

        <section class="fr-section">
            <div class="fr-section-head"><div><h3>საწყობის შეფასება</h3><p>მიმდინარე მარაგის ღირებულება — არჩეული პერიოდისგან დამოუკიდებლად</p></div></div>
            <div class="fr-grid">
                <article class="fr-card"><div class="fr-card-top"><span class="fr-card-label">მარაგის თვითღირებულება</span><span class="fr-icon">₾</span></div><div class="fr-value">{{ number_format($inventoryCostValue, 2) }} ₾</div><div class="fr-foot">ცნობილი თვითღირებულების მარაგი</div></article>
                <article class="fr-card"><div class="fr-card-top"><span class="fr-card-label">გასაყიდი ღირებულება</span><span class="fr-icon">↗</span></div><div class="fr-value">{{ number_format($inventoryRetailValue, 2) }} ₾</div><div class="fr-foot">სტანდარტული გასაყიდი ფასებით</div></article>
                <article class="fr-card"><div class="fr-card-top"><span class="fr-card-label">მარაგის რაოდენობა</span><span class="fr-icon">▦</span></div><div class="fr-value">{{ number_format($inventoryUnits) }}</div><div class="fr-foot">ერთეული საწყობში</div></article>
                <article class="fr-card"><div class="fr-card-top"><span class="fr-card-label">უცნობი თვითღირებულება</span><span class="fr-icon">!</span></div><div class="fr-value {{ $unknownCostUnits > 0 ? 'amber' : 'green' }}">{{ number_format($unknownCostUnits) }}</div><div class="fr-foot">{{ $unknownCostProductsCount }} პროდუქტი საჭიროებს შევსებას</div></article>
            </div>
            @if ($unknownCostProductsCount > 0)
                <div class="fr-warning"><strong>ყურადღება:</strong> {{ number_format($unknownCostUnits) }} ერთეულს თვითღირებულება არ აქვს მითითებული და მარაგის თვითღირებულების ჯამში არ შედის.</div>
            @endif
            <div class="fr-panel">
                <div class="fr-panel-head"><strong>მარაგი კატეგორიების მიხედვით</strong><span>{{ $inventoryByCategory->count() }} კატეგორია</span></div>
                <div class="fr-table-wrap"><table class="fr-table">
                    <thead><tr><th>კატეგორია</th><th class="num">პროდუქტები</th><th class="num">ერთეულები</th><th class="num">თვითღირებულება</th><th class="num">გასაყიდი ღირებულება</th><th class="num">უცნობი თვითღ.</th></tr></thead>
                    <tbody>
                    @forelse ($inventoryByCategory as $row)
                        <tr><td><strong>{{ $row['category'] }}</strong></td><td class="num">{{ number_format($row['products']) }}</td><td class="num">{{ number_format($row['units']) }}</td><td class="num">{{ number_format($row['cost_value'], 2) }} ₾</td><td class="num">{{ number_format($row['retail_value'], 2) }} ₾</td><td class="num">{{ number_format($row['unknown_cost_units']) }}</td></tr>
                    @empty<tr><td colspan="6" class="fr-empty">საწყობში მარაგი არ არის.</td></tr>@endforelse
                    </tbody>
                </table></div>
            </div>
            <p class="fr-note">შეფასება ეფუძნება პროდუქტზე შენახულ თვითღირებულებასა და სტანდარტულ ფასს. ტრანსპორტირება, საბაჟო და სხვა დამატებითი ხარჯები გათვალისწინებული არ არის, თუ ისინი პროდუქტის თვითღირებულებაში არ არის ასახული.</p>
        </section>

        <section class="fr-section">
            <div class="fr-section-head"><div><h3>საოპერაციო ხარჯები</h3><p>არჩეულ პერიოდში დარიცხული ხარჯები და მათი გადახდის მდგომარეობა</p></div></div>
            <div class="fr-grid">
                <article class="fr-card"><div class="fr-card-top"><span class="fr-card-label">ხარჯების ჯამი</span><span class="fr-icon">−</span></div><div class="fr-value">{{ number_format($operatingExpenses, 2) }} ₾</div><div class="fr-foot">ყველა აღრიცხული ხარჯი</div></article>
                <article class="fr-card"><div class="fr-card-top"><span class="fr-card-label">გადახდილი</span><span class="fr-icon">✓</span></div><div class="fr-value green">{{ number_format($paidExpenses, 2) }} ₾</div><div class="fr-foot">უკვე გადახდილი თანხა</div></article>
                <article class="fr-card"><div class="fr-card-top"><span class="fr-card-label">გადასახდელი</span><span class="fr-icon">◷</span></div><div class="fr-value amber">{{ number_format($unpaidExpenses, 2) }} ₾</div><div class="fr-foot">დარჩენილი ვალდებულება</div></article>
                <article class="fr-card"><div class="fr-card-top"><span class="fr-card-label">საოპერაციო შედეგი*</span><span class="fr-icon">∑</span></div><div class="fr-value {{ $operatingResult < 0 ? 'red' : 'green' }}">{{ number_format($operatingResult, 2) }} ₾</div><div class="fr-foot">მთლიანი მოგება მინუს ხარჯები</div></article>
            </div>
            <div class="fr-split">
                <div class="fr-panel">
                    <div class="fr-panel-head"><strong>ხარჯები კატეგორიების მიხედვით</strong><span>{{ $expensesByCategory->count() }} კატეგორია</span></div>
                    @php $maxExpense = max(1, (float) $expensesByCategory->max('total')); @endphp
                    <div class="fr-bars">
                        @forelse ($expensesByCategory as $row)
                            <div class="fr-bar-row"><div class="fr-bar-name">{{ $row['category'] }}</div><div class="fr-track"><div class="fr-fill" style="width: {{ min(100, ($row['total'] / $maxExpense) * 100) }}%"></div></div><div class="fr-bar-amount">{{ number_format($row['total'], 0) }} ₾</div></div>
                        @empty<div class="fr-empty">არჩეულ პერიოდში ხარჯები არ არის.</div>@endforelse
                    </div>
                </div>
                <div class="fr-panel">
                    <div class="fr-panel-head"><strong>გადახდის მდგომარეობა</strong><span>ხარჯების განაწილება</span></div>
                    <div class="fr-bars">
                        @php $expenseTotal = max(1, $operatingExpenses); $paidWidth = min(100, ($paidExpenses / $expenseTotal) * 100); @endphp
                        <div class="fr-bar-row"><div class="fr-bar-name">გადახდილი</div><div class="fr-track"><div class="fr-fill" style="width: {{ $paidWidth }}%;background:linear-gradient(90deg,#059669,#34d399)"></div></div><div class="fr-bar-amount">{{ number_format($paidExpenses, 2) }} ₾</div></div>
                        <div class="fr-bar-row"><div class="fr-bar-name">გადასახდელი</div><div class="fr-track"><div class="fr-fill" style="width: {{ min(100, ($unpaidExpenses / $expenseTotal) * 100) }}%;background:linear-gradient(90deg,#d97706,#fbbf24)"></div></div><div class="fr-bar-amount">{{ number_format($unpaidExpenses, 2) }} ₾</div></div>
                        <p class="fr-note">ხარჯი ანგარიშში აისახება ხარჯის თარიღით, მიუხედავად გადახდის სტატუსისა.</p>
                    </div>
                </div>
            </div>
            <div class="fr-panel">
                <div class="fr-panel-head"><strong>ხარჯების დეტალური სია</strong><span>{{ $expenses->count() }} ჩანაწერი</span></div>
                <div class="fr-table-wrap"><table class="fr-table">
                    <thead><tr><th>თარიღი</th><th>აღწერა</th><th>კატეგორია</th><th class="num">თანხა</th><th>სტატუსი</th><th>გადახდის მეთოდი</th></tr></thead>
                    <tbody>
                    @forelse ($expenses as $expense)
                        <tr><td>{{ $expense->expense_date?->format('d.m.Y') }}</td><td><strong>{{ $expense->description }}</strong></td><td>{{ \App\Models\OperatingExpense::categories()[$expense->category] ?? $expense->category }}</td><td class="num">{{ number_format((float) $expense->amount, 2) }} ₾</td><td><span class="fr-pill {{ $expense->status === 'paid' ? 'paid' : 'unpaid' }}">{{ $expense->status === 'paid' ? 'გადახდილი' : 'გადასახდელი' }}</span></td><td>{{ \App\Models\OperatingExpense::paymentMethods()[$expense->payment_method] ?? '—' }}</td></tr>
                    @empty<tr><td colspan="6" class="fr-empty">არჩეულ პერიოდში ხარჯები არ არის.</td></tr>@endforelse
                    </tbody>
                </table></div>
            </div>
        </section>

        <section class="fr-section">
            <div class="fr-section-head"><div><h3>გაყიდვების ფინანსური ანგარიში</h3><p>{{ $from }} — {{ $until }} · მხოლოდ დასრულებული და თვითღირებულებით სრულად აღრიცხული შეკვეთები</p></div></div>
            <div class="fr-grid">
                <article class="fr-card"><div class="fr-card-top"><span class="fr-card-label">დასრულებული შეკვეთები</span><span class="fr-icon">▤</span></div><div class="fr-value">{{ number_format($orderCount) }}</div><div class="fr-foot">სრულად აღრიცხული</div></article>
                <article class="fr-card"><div class="fr-card-top"><span class="fr-card-label">გაყიდვები</span><span class="fr-icon">₾</span></div><div class="fr-value">{{ number_format($revenue, 2) }} ₾</div><div class="fr-foot">საბოლოო შეკვეთის თანხა</div></article>
                <article class="fr-card"><div class="fr-card-top"><span class="fr-card-label">საქონლის თვითღირებულება</span><span class="fr-icon">▦</span></div><div class="fr-value">{{ number_format($cost, 2) }} ₾</div><div class="fr-foot">გაყიდული საქონლის ხარჯი</div></article>
                <article class="fr-card"><div class="fr-card-top"><span class="fr-card-label">მთლიანი მოგება</span><span class="fr-icon">↗</span></div><div class="fr-value {{ $grossProfit < 0 ? 'red' : 'green' }}">{{ number_format($grossProfit, 2) }} ₾</div><div class="fr-foot">გაყიდვები მინუს თვითღირებულება</div></article>
            </div>
            <div class="fr-split">
                <div class="fr-panel">
                    <div class="fr-panel-head"><strong>დღიური გაყიდვები</strong><span>დასრულების თარიღით</span></div>
                    @php $chartDays = $daily->take(12)->reverse()->values(); $maxDailyRevenue = max(1, (float) $chartDays->max('revenue')); @endphp
                    <div class="fr-chart">
                        @forelse ($chartDays as $day)
                            <div class="fr-chart-item"><span class="fr-chart-value">{{ number_format($day['revenue'], 0) }}</span><div class="fr-chart-bar" style="height: {{ max(2, ($day['revenue'] / $maxDailyRevenue) * 100) }}%"></div><span class="fr-chart-date">{{ \Carbon\Carbon::parse($day['date'])->format('d.m') }}</span></div>
                        @empty<div class="fr-empty">არჩეულ პერიოდში გაყიდვები არ არის.</div>@endforelse
                    </div>
                </div>
                <div class="fr-panel">
                    <div class="fr-panel-head"><strong>დამატებითი მაჩვენებლები</strong><span>პერიოდის ჯამი</span></div>
                    <div class="fr-bars">
                        <div class="fr-bar-row"><div class="fr-bar-name">ფასდაკლებები</div><div class="fr-track"><div class="fr-fill" style="width:{{ $revenue > 0 ? min(100, ($discounts / $revenue) * 100) : 0 }}%"></div></div><div class="fr-bar-amount">{{ number_format($discounts, 2) }} ₾</div></div>
                        <div class="fr-warning">თვითღირებულება არასრულია {{ number_format($incompleteCount) }} შეკვეთაზე. ეს შეკვეთები მოგების გამოთვლაში არ შედის.</div>
                    </div>
                </div>
            </div>
            <div class="fr-panel">
                <div class="fr-panel-head"><strong>დასრულებული შეკვეთები</strong><span>{{ $orders->count() }} შეკვეთა</span></div>
                <div class="fr-table-wrap"><table class="fr-table">
                    <thead><tr><th>შეკვეთა</th><th>მომხმარებელი</th><th class="num">გაყიდვა</th><th class="num">თვითღირებულება</th><th class="num">მთლიანი მოგება</th><th class="num">დასრულდა</th></tr></thead>
                    <tbody>
                    @forelse ($orders as $order)
                        @php $orderCost = (float) $order->items->sum(fn ($item) => (float) $item->unit_cost * (int) $item->quantity); $profit = (float) $order->total - $orderCost; @endphp
                        <tr><td><strong>{{ $order->order_number }}</strong></td><td>{{ $order->customer_name }}</td><td class="num">{{ number_format((float) $order->total, 2) }} ₾</td><td class="num">{{ number_format($orderCost, 2) }} ₾</td><td class="num"><strong style="color:{{ $profit < 0 ? '#dc4b55' : '#079669' }}">{{ number_format($profit, 2) }} ₾</strong></td><td class="num">{{ $order->updated_at?->format('d.m.Y H:i') }}</td></tr>
                    @empty<tr><td colspan="6" class="fr-empty">არჩეულ პერიოდში აღრიცხული დასრულებული შეკვეთები არ მოიძებნა.</td></tr>@endforelse
                    </tbody>
                </table></div>
            </div>
            <p class="fr-note">* საოპერაციო შედეგი = სრულად აღრიცხული შეკვეთების მთლიანი მოგება − საოპერაციო ხარჯები. ეს არ არის წმინდა მოგება: გადასახადები, გადახდის საკომისიოები და სხვა ცალკე აღურიცხავი ხარჯები არ არის გამოკლებული. პერიოდი შეკვეთის ბოლო განახლების თარიღით განისაზღვრება.</p>
        </section>
    </div>
</x-filament-panels::page>
