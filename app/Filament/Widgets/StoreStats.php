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
        $revenue = Order::whereIn('status', ['confirmed', 'processing', 'shipped', 'completed'])->sum('total');

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

            Stat::make('შეკვეთების ბრუნვა', number_format((float) $revenue, 2).' ₾')
                ->description('დადასტურებული და შესრულების პროცესში')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }
}
