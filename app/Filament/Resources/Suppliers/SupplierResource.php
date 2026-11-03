<?php

namespace App\Filament\Resources\Suppliers;

use App\Filament\Resources\Suppliers\Pages\CreateSupplier;
use App\Filament\Resources\Suppliers\Pages\EditSupplier;
use App\Filament\Resources\Suppliers\Pages\ListSuppliers;
use App\Models\Supplier;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationLabel = 'მომწოდებლები';
    protected static ?string $modelLabel = 'მომწოდებელი';
    protected static ?string $pluralModelLabel = 'მომწოდებლები';
    protected static string|\UnitEnum|null $navigationGroup = 'შესყიდვები';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('კომპანიის დასახელება')->required()->maxLength(255),
            TextInput::make('contact_person')->label('საკონტაქტო პირი')->maxLength(255),
            TextInput::make('phone')->label('ტელეფონი')->tel()->maxLength(50),
            TextInput::make('email')->label('ელფოსტა')->email()->maxLength(255),
            TextInput::make('tax_number')->label('საიდენტიფიკაციო კოდი')->maxLength(100),
            TextInput::make('address')->label('მისამართი')->maxLength(255),
            Textarea::make('notes')->label('შენიშვნა')->rows(3)->columnSpanFull(),
            Toggle::make('is_active')->label('აქტიურია')->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('მომწოდებელი')->searchable()->sortable()->weight('bold'),
            TextColumn::make('contact_person')->label('საკონტაქტო პირი')->searchable(),
            TextColumn::make('phone')->label('ტელეფონი')->searchable()->copyable(),
            TextColumn::make('email')->label('ელფოსტა')->searchable()->toggleable(),
            TextColumn::make('purchase_orders_count')->counts('purchaseOrders')->label('შესყიდვები')->sortable(),
            IconColumn::make('is_active')->label('აქტიური')->boolean(),
        ])->filters([
            TernaryFilter::make('is_active')->label('აქტიურობა'),
        ])->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSuppliers::route('/'),
            'create' => CreateSupplier::route('/create'),
            'edit' => EditSupplier::route('/{record}/edit'),
        ];
    }
}
