<?php

namespace App\Filament\Pages;

use App\Models\OperatingExpense;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Filament\Pages\Page;

class FinancialReport extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static ?string $navigationLabel = 'ფინანსური ანგარიში';

    protected static ?string $title = 'ფინანსური ანგარიში';

    protected static string|\UnitEnum|null $navigationGroup = 'ფინანსები';

    protected static ?int $navigationSort = 20;

    protected string $view = 'filament.pages.financial-report';

    protected function getViewData(): array
    {
        $fromInput = request()->query('from');
        $untilInput = request()->query('until');

        try {
            $from = $fromInput ? Carbon::createFromFormat('Y-m-d', $fromInput)->startOfDay() : now()->startOfMonth();
        } catch (\Throwable) {
            $from = now()->startOfMonth();
        }

        try {
            $until = $untilInput ? Carbon::createFromFormat('Y-m-d', $untilInput)->endOfDay() : now()->endOfMonth();
        } catch (\Throwable) {
            $until = now()->endOfMonth();
        }

        if ($from->gt($until)) {
            [$from, $until] = [$until->copy()->startOfDay(), $from->copy()->endOfDay()];
        }

        $completedOrders = Order::query()
            ->where('status', 'completed')
            ->whereBetween('updated_at', [$from, $until])
            ->whereHas('items')
            ->whereDoesntHave('items', fn ($query) => $query->whereNull('unit_cost'));

        $orders = (clone $completedOrders)
            ->with('items:id,order_id,product_name,quantity,unit_cost,line_total')
            ->latest('updated_at')
            ->get(['id', 'order_number', 'customer_name', 'total', 'discount_amount', 'updated_at']);

        $revenue = (float) $orders->sum(fn (Order $order): float => (float) $order->total);
        $discounts = (float) $orders->sum(fn (Order $order): float => (float) $order->discount_amount);
        $cost = (float) $orders->sum(
            fn (Order $order): float => (float) $order->items->sum(
                fn ($item): float => (float) $item->unit_cost * (int) $item->quantity
            )
        );

        $incompleteCount = Order::query()
            ->where('status', 'completed')
            ->whereBetween('updated_at', [$from, $until])
            ->where(function ($query): void {
                $query->whereDoesntHave('items')
                    ->orWhereHas('items', fn ($items) => $items->whereNull('unit_cost'));
            })
            ->count();

        $daily = $orders
            ->groupBy(fn (Order $order): string => $order->updated_at->format('Y-m-d'))
            ->map(fn ($dayOrders, string $date): array => [
                'date' => $date,
                'orders' => $dayOrders->count(),
                'revenue' => (float) $dayOrders->sum('total'),
                'profit' => (float) $dayOrders->sum(fn (Order $order): float =>
                    (float) $order->total - (float) $order->items->sum(
                        fn ($item): float => (float) $item->unit_cost * (int) $item->quantity
                    )
                ),
            ])
            ->sortKeysDesc()
            ->values();

        $expenses = OperatingExpense::query()
            ->whereBetween('expense_date', [$from->toDateString(), $until->toDateString()])
            ->get();

        $operatingExpenses = (float) $expenses->sum('amount');
        $paidExpenses = (float) $expenses->where('status', 'paid')->sum('amount');
        $unpaidExpenses = (float) $expenses->where('status', 'unpaid')->sum('amount');
        $operatingResult = $revenue - $cost - $operatingExpenses;

        $expensesByCategory = $expenses
            ->groupBy(fn (OperatingExpense $expense): string => OperatingExpense::categories()[$expense->category] ?? $expense->category)
            ->map(fn ($items, string $category): array => [
                'category' => $category,
                'count' => $items->count(),
                'total' => (float) $items->sum('amount'),
                'paid' => (float) $items->where('status', 'paid')->sum('amount'),
                'unpaid' => (float) $items->where('status', 'unpaid')->sum('amount'),
            ])
            ->sortByDesc('total')
            ->values();

        // Inventory valuation is a current snapshot, independent of the selected sales period.
        $inventoryProducts = Product::query()
            ->with('category:id,name')
            ->where('stock', '>', 0)
            ->get(['id', 'category_id', 'name', 'sku', 'stock', 'price', 'cost_price']);

        $inventoryUnits = (int) $inventoryProducts->sum('stock');
        $inventoryCostValue = (float) $inventoryProducts
            ->filter(fn (Product $product): bool => $product->cost_price !== null)
            ->sum(fn (Product $product): float => (int) $product->stock * (float) $product->cost_price);
        $inventoryRetailValue = (float) $inventoryProducts
            ->sum(fn (Product $product): float => (int) $product->stock * (float) $product->price);
        $unknownCostProducts = $inventoryProducts
            ->filter(fn (Product $product): bool => $product->cost_price === null);
        $unknownCostUnits = (int) $unknownCostProducts->sum('stock');

        $inventoryByCategory = $inventoryProducts
            ->groupBy(fn (Product $product): string => $product->category?->name ?? 'კატეგორიის გარეშე')
            ->map(function ($products, string $category): array {
                $knownCostProducts = $products->filter(fn (Product $product): bool => $product->cost_price !== null);

                return [
                    'category' => $category,
                    'products' => $products->count(),
                    'units' => (int) $products->sum('stock'),
                    'cost_value' => (float) $knownCostProducts->sum(
                        fn (Product $product): float => (int) $product->stock * (float) $product->cost_price
                    ),
                    'retail_value' => (float) $products->sum(
                        fn (Product $product): float => (int) $product->stock * (float) $product->price
                    ),
                    'unknown_cost_units' => (int) $products
                        ->filter(fn (Product $product): bool => $product->cost_price === null)
                        ->sum('stock'),
                ];
            })
            ->sortByDesc('cost_value')
            ->values();

        return [
            'from' => $from->format('Y-m-d'),
            'until' => $until->format('Y-m-d'),
            'orders' => $orders,
            'orderCount' => $orders->count(),
            'revenue' => $revenue,
            'cost' => $cost,
            'grossProfit' => $revenue - $cost,
            'discounts' => $discounts,
            'incompleteCount' => $incompleteCount,
            'daily' => $daily,
            'expenses' => $expenses->sortByDesc('expense_date')->values(),
            'operatingExpenses' => $operatingExpenses,
            'paidExpenses' => $paidExpenses,
            'unpaidExpenses' => $unpaidExpenses,
            'operatingResult' => $operatingResult,
            'expensesByCategory' => $expensesByCategory,
            'inventoryUnits' => $inventoryUnits,
            'inventoryCostValue' => $inventoryCostValue,
            'inventoryRetailValue' => $inventoryRetailValue,
            'unknownCostProductsCount' => $unknownCostProducts->count(),
            'unknownCostUnits' => $unknownCostUnits,
            'inventoryByCategory' => $inventoryByCategory,
        ];
    }
}
