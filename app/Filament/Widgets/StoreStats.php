<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StoreStats extends BaseWidget
{
    protected function getStats(): array
    {
        $newOrders = Order::where('status', 'new')->count();
        $todayOrders = Order::whereDate('created_at', today())->count();
        $lowStock = Product::where('is_active', true)->where('stock', '<=', 5)->count();
        $activeOrderValue = Order::whereIn('status', ['confirmed', 'processing', 'shipped'])->sum('total');
        $completedRevenue = Order::where('status', 'completed')->sum('total');
        $monthCompletedRevenue = Order::where('status', 'completed')
            ->whereBetween('updated_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('total');
        $completedCost = \\App\\Models\\OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', 'completed')
            ->sum(\\Illuminate\\Support\\Facades\\DB::raw('COALESCE(order_items.unit_cost, 0) * order_items.quantity'));
        $completedGrossProfit = (float) $completedRevenue - (float) $completedCost;
        $monthCompletedCost = \\App\\Models\\OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', 'completed')
            ->whereBetween('orders.updated_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum(\\Illuminate\\Support\\Facades\\DB::raw('COALESCE(order_items.unit_cost, 0) * order_items.quantity'));
        $monthGrossProfit = (float) $monthCompletedRevenue - (float) $monthCompletedCost;
        $completedDiscounts = Order::where('status', 'completed')->sum('discount_amount');

        return [
            Stat::make('პროდუქტები', Product::count())
                ->description('კატალოგში არსებული პროდუქცია')
                ->descriptionIcon('heroicon-m-cube')
                ->color('primary'),

            Stat::make('აქტიური პროდუქტები', Product::where('is_active', true)->count())
                ->description('მომხმარებლისთვის ხილული')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('ახალი შეკვეთები', $newOrders)
                ->description('დამუშავებას ელოდება')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color($newOrders > 0 ? 'warning' : 'gray'),

            Stat::make('დღევანდელი შეკვეთები', $todayOrders)
                ->description('დღეს მიღებული შეკვეთები')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),

            Stat::make('დაბალი მარაგი', $lowStock)
                ->description('5 ან ნაკლები ერთეული')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($lowStock > 0 ? 'danger' : 'success'),

            Stat::make('აქტიური შეკვეთების ღირებულება', number_format((float) $activeOrderValue, 2).' ₾')
                ->description('დადასტურებული, მუშავდება და გაგზავნილია')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('info'),

            Stat::make('დასრულებული შეკვეთები', number_format((float) $completedRevenue, 2).' ₾')
                ->description('დასრულებული შეკვეთების ჯამი')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('ამ თვის დასრულებული გაყიდვები', number_format((float) $monthCompletedRevenue, 2).' ₾')
                ->description('მიმდინარე თვეში დასრულებული შეკვეთები')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('success'),

            Stat::make('დასრულებული შეკვეთების მთლიანი მოგება', number_format($completedGrossProfit, 2).' ₾')
                ->description('გაყიდვის თანხა მინუს დაფიქსირებული თვითღირებულება')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($completedGrossProfit < 0 ? 'danger' : 'success'),

            Stat::make('ამ თვის მთლიანი მოგება', number_format($monthGrossProfit, 2).' ₾')
                ->description('მიმდინარე თვეში დასრულებული შეკვეთები')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color($monthGrossProfit < 0 ? 'danger' : 'success'),

            Stat::make('დასრულებული შეკვეთების ფასდაკლება', number_format((float) $completedDiscounts, 2).' ₾')
                ->description('კუპონები და Rewards ქულები')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('warning'),
        ];
    }
}
