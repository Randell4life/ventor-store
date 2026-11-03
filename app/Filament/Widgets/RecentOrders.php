<?php

namespace App\\Filament\\Widgets;

use App\\Filament\\Resources\\Orders\\OrderResource;
use App\\Models\\Order;
use Filament\\Tables\\Columns\\TextColumn;
use Filament\\Tables\\Table;
use Filament\\Widgets\\TableWidget;

class RecentOrders extends TableWidget
{
    protected static ?string $heading = 'ბოლო შეკვეთები';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Order::query()->latest())
            ->columns([
                TextColumn::make('order_number')
                    ->label('შეკვეთა')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('customer_name')
                    ->label('მომხმარებელი')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('ტელეფონი'),
                TextColumn::make('total')
                    ->label('ჯამი')
                    ->money('GEL')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('სტატუსი')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'new' => 'ახალი',
                        'confirmed' => 'დადასტურებული',
                        'processing' => 'მუშავდება',
                        'shipped' => 'გაგზავნილია',
                        'completed' => 'დასრულებული',
                        'cancelled' => 'გაუქმებული',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'warning',
                        'confirmed' => 'info',
                        'processing' => 'primary',
                        'shipped', 'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('თარიღი')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->recordUrl(fn (Order $record): string => OrderResource::getUrl('edit', ['record' => $record]))
            ->defaultSort('created_at', 'desc')
            ->paginated([5, 10]);
    }
}
