<?php

namespace App\\Filament\\Widgets;

use App\\Models\\Order;
use App\\Models\\Product;
use Filament\\Widgets\\StatsOverviewWidget;
use Filament\\Widgets\\StatsOverviewWidget\\Stat;

class VentorOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $todayOrders = Order::query()->whereDate('created_at', today());
        $monthOrders = Order::query()->whereBetween('created_at', [
            now()->copy()->startOfMonth(),
            now()->copy()->endOfMonth(),
        ]);

        $activeProducts = Product::query()->where('is_active', true)->count();
        $lowStock = Product::query()
            ->where('is_active', true)
            ->where('stock', '<=', 3)
            ->count();

        return [
            Stat::make('დღევანდელი შეკვეთები', (clone $todayOrders)->count())
                ->description('დღეს მიღებული შეკვეთები')
                ->color('primary'),
            Stat::make('დღევანდელი გაყიდვები', $this->money((float) (clone $todayOrders)->sum('total')))
                ->description('დღევანდელი შეკვეთების ჯამი')
                ->color('success'),
            Stat::make('ამ თვის შეკვეთები', (clone $monthOrders)->count())
                ->description(now()->translatedFormat('F Y'))
                ->color('primary'),
            Stat::make('ამ თვის გაყიდვები', $this->money((float) (clone $monthOrders)->sum('total')))
                ->description('მიმდინარე თვის შეკვეთების ჯამი')
                ->color('success'),
            Stat::make('აქტიური პროდუქტები', $activeProducts)
                ->description('კატალოგში გამოქვეყნებული პროდუქტები')
                ->color('info'),
            Stat::make('დაბალი მარაგი', $lowStock)
                ->description('აქტიური პროდუქტები — 3 ან ნაკლები')
                ->color($lowStock > 0 ? 'warning' : 'success'),
        ];
    }

    private function money(float $amount): string
    {
        return number_format($amount, 2, '.', ',') . ' ₾';
    }
}
