<?php

namespace App\\Filament\\Resources\\Products;

use App\\Filament\\Resources\\Products\\Pages\\CreateProduct;
use App\\Filament\\Resources\\Products\\Pages\\EditProduct;
use App\\Filament\\Resources\\Products\\Pages\\ListProducts;
use App\\Models\\Product;
use Filament\\Forms\\Components\\FileUpload;
use Filament\\Forms\\Components\\Select;
use Filament\\Forms\\Components\\Textarea;
use Filament\\Forms\\Components\\TextInput;
use Filament\\Forms\\Components\\Toggle;
use Filament\\Resources\\Resource;
use Filament\\Schemas\\Schema;
use Filament\\Tables\\Columns\\ImageColumn;
use Filament\\Tables\\Columns\\IconColumn;
use Filament\\Tables\\Columns\\TextColumn;
use Filament\\Tables\\Filters\\Filter;
use Filament\\Tables\\Filters\\SelectFilter;
use Filament\\Tables\\Filters\\TernaryFilter;
use Filament\\Tables\\Table;
use Illuminate\\Database\\Eloquent\\Builder;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $recordTitleAttribute = 'name';
    protected static string|\\BackedEnum|null $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationLabel = 'პროდუქტები';
    protected static ?string $modelLabel = 'პროდუქტი';
    protected static ?string $pluralModelLabel = 'პროდუქტები';
    protected static string|\\UnitEnum|null $navigationGroup = 'კატალოგი';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('category_id')->label('კატეგორია')->relationship('category', 'name')->searchable()->preload()->required(),
            TextInput::make('name')->label('დასახელება')->required()->maxLength(255),
            TextInput::make('sku')->label('SKU')->maxLength(100),
            TextInput::make('brand')->label('ბრენდი')->maxLength(120),
            TextInput::make('volume')->label('მოცულობა')->maxLength(80),
            TextInput::make('price')->label('გასაყიდი ფასი (₾)')->numeric()->minValue(0)->required(),
            TextInput::make('compare_at_price')->label('ძველი ფასი (₾)')->numeric()->minValue(0),
            TextInput::make('cost_price')->label('თვითღირებულება (₾)')->numeric()->minValue(0),
            TextInput::make('stock')->label('მარაგი')->numeric()->integer()->minValue(0)->required()->default(0),
            FileUpload::make('image_path')
                ->label('პროდუქტის ფოტო')
                ->image()
                ->disk('public')
                ->directory('products')
                ->visibility('public')
                ->maxSize(5120),
            Textarea::make('short_description')->label('მოკლე აღწერა')->rows(2)->columnSpanFull(),
            Textarea::make('description')->label('სრული აღწერა')->rows(6)->columnSpanFull(),
            Toggle::make('is_active')->label('აქტიურია')->default(false),
            Toggle::make('is_featured')->label('რჩეული პროდუქტი')->default(false),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            ImageColumn::make('image_path')->label('ფოტო')->disk('public')->square(),
            TextColumn::make('name')->label('პროდუქტი')->searchable()->sortable()->weight('bold'),
            TextColumn::make('category.name')->label('კატეგორია')->sortable(),
            TextColumn::make('sku')->label('SKU')->searchable(),
            TextColumn::make('brand')->label('ბრენდი')->searchable(),
            TextColumn::make('price')->label('ფასი')->money('GEL')->sortable(),
            TextColumn::make('stock')->label('მარაგი')->numeric()->sortable()
                ->badge()
                ->color(fn (int $state): string => match (true) {
                    $state <= 1 => 'danger',
                    $state <= 5 => 'warning',
                    default => 'success',
                }),
            IconColumn::make('is_active')->label('აქტიური')->boolean(),
            IconColumn::make('is_featured')->label('რჩეული')->boolean(),
        ])->filters([
            SelectFilter::make('category')->label('კატეგორია')->relationship('category', 'name'),
            TernaryFilter::make('is_active')->label('აქტიურობა'),
            TernaryFilter::make('is_featured')->label('რჩეული პროდუქტი'),
            Filter::make('low_stock')
                ->label('დაბალი მარაგი')
                ->query(fn (Builder $query): Builder => $query->where('stock', '<=', 5)),
        ])->recordActions([
            \\Filament\\Actions\\EditAction::make()->label('რედაქტირება'),
            \\Filament\\Actions\\DeleteAction::make()->label('წაშლა'),
        ])->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }
}
