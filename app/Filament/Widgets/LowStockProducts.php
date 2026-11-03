<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Products\ProductResource;
use App\Models\Product;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LowStockProducts extends TableWidget
{
    protected static ?string $heading = 'მარაგის შევსება საჭიროა';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Product::query()->where('is_active', true)->where('stock', '<=', 5)->orderBy('stock'))
            ->columns([
                TextColumn::make('name')
                    ->label('პროდუქტი')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('sku')
                    ->label('SKU'),
                TextColumn::make('category.name')
                    ->label('კატეგორია'),
                TextColumn::make('stock')
                    ->label('დარჩენილი რაოდენობა')
                    ->numeric()
                    ->badge()
                    ->color(fn (int $state): string => $state <= 1 ? 'danger' : 'warning'),
                TextColumn::make('price')
                    ->label('ფასი')
                    ->money('GEL'),
            ])
            ->recordUrl(fn (Product $record): string => ProductResource::getUrl('edit', ['record' => $record]))
            ->paginated([5, 10])
            ->emptyStateHeading('ყველა აქტიურ პროდუქტს საკმარისი მარაგი აქვს')
            ->emptyStateDescription('როცა მარაგი 5 ან ნაკლებ ერთეულამდე ჩამოვა, პროდუქტი აქ გამოჩნდება.');
    }
}
