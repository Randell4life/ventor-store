<?php

namespace App\\Filament\\Pages;

use App\\Models\\Order;
use App\\Models\\OrderItem;
use Carbon\\Carbon;
use Filament\\Pages\\Page;
use Illuminate\\Support\\Facades\\DB;

class FinancialReport extends Page
{
    protected static string|\\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static ?string $navigationLabel = 'ფინანსური ანგარიში';

    protected static ?string $title = 'ფინანსური ანგარიში';

    protected static string|\\UnitEnum|null $navigationGroup = 'გაყიდვები';

    protected static ?int $navigationSort = 20;

    protected string $view = 'filament.pages.financial-report';

    protected function getViewData(): array
    {
        $fromInput = request()->query('from');
        $untilInput = request()->query('until');

        try {
            $from = $fromInput ? Carbon::createFromFormat('Y-m-d', $fromInput)->startOfDay() : now()->startOfMonth();
        } catch (\\Throwable) {
            $from = now()->startOfMonth();
        }

        try {
            $until = $untilInput ? Carbon::createFromFormat('Y-m-d', $untilInput)->endOfDay() : now()->endOfMonth();
        } catch (\\Throwable) {
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
        ];
    }
}
