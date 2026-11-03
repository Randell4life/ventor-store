<?php

namespace App\Filament\Resources\InventoryMovements;

use App\Filament\Resources\InventoryMovements\Pages\ListInventoryMovements;
use App\Models\InventoryMovement;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class InventoryMovementResource extends Resource
{
    protected static ?string $model = InventoryMovement::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';
    protected static ?string $navigationLabel = 'მარაგის ისტორია';
    protected static ?string $modelLabel = 'მარაგის მოძრაობა';
    protected static ?string $pluralModelLabel = 'მარაგის ისტორია';
    protected static string|\UnitEnum|null $navigationGroup = 'საწყობი';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('reference')->label('დოკუმენტი')->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('created_at')->label('თარიღი')->dateTime('d.m.Y H:i')->sortable(),
            TextColumn::make('product.name')->label('პროდუქტი')->searchable()->sortable()->weight('bold'),
            TextColumn::make('product.sku')->label('SKU')->searchable(),
            TextColumn::make('movement_type')->label('ოპერაცია')->badge()
                ->formatStateUsing(fn (string $state): string => match ($state) {
                    'sale' => 'გაყიდვა',
                    'restock' => 'მარაგის შევსება',
                    'adjustment' => 'კორექტირება',
                    default => $state,
                })
                ->color(fn (string $state): string => match ($state) {
                    'sale' => 'warning',
                    'restock' => 'success',
                    'adjustment' => 'info',
                    default => 'gray',
                }),
            TextColumn::make('quantity_change')->label('ცვლილება')->numeric()->sortable()
                ->color(fn (int $state): string => $state < 0 ? 'danger' : 'success'),
            TextColumn::make('stock_before')->label('მანამდე')->numeric(),
            TextColumn::make('stock_after')->label('შემდეგ')->numeric(),
            TextColumn::make('reference')->label('დოკუმენტი')->searchable()->placeholder('—'),
            TextColumn::make('note')->label('შენიშვნა')->limit(45)->placeholder('—')->toggleable(),
        ])->filters([
            SelectFilter::make('movement_type')->label('ოპერაცია')->options([
                'sale' => 'გაყიდვა',
                'restock' => 'მარაგის შევსება',
                'adjustment' => 'კორექტირება',
            ]),
        ])->defaultSort('created_at', 'desc')->paginated([25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInventoryMovements::route('/'),
        ];
    }
}
