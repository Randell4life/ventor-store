<?php

namespace App\Filament\Resources\Products;

use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Models\InventoryMovement;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $recordTitleAttribute = 'name';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationLabel = 'პროდუქტები';
    protected static ?string $modelLabel = 'პროდუქტი';
    protected static ?string $pluralModelLabel = 'პროდუქტები';
    protected static string|\UnitEnum|null $navigationGroup = 'კატალოგი';
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
            TextInput::make('compare_at_price')->label('ძველი ფასი (₾)')->numeric()->minValue(0)
                ->helperText('მიუთითე მიმდინარე ფასზე მაღალი ფასი, რომ ფასდაკლება გამოჩნდეს.'),
            DateTimePicker::make('discount_starts_at')->label('აქციის დაწყება')->seconds(false)->native(false),
            DateTimePicker::make('discount_ends_at')->label('აქციის დასრულება')->seconds(false)->native(false)
                ->after('discount_starts_at'),
            TextInput::make('cost_price')->label('თვითღირებულება (₾)')->numeric()->minValue(0)
                ->helperText('გამოიყენება სავარაუდო მოგებისა და მარჟის გამოსათვლელად.'),
            TextInput::make('stock')->label('საწყისი მარაგი')->numeric()->integer()->minValue(0)->required()->default(0)
                ->helperText('არსებული პროდუქტის მარაგის ცვლილება გააკეთე პროდუქტის სიიდან — „მარაგის მართვა“ მოქმედებით, რათა ისტორია სწორად აღირიცხოს.'),
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
            TextColumn::make('discount_ends_at')->label('აქციის დასრულება')->dateTime('d.m.Y H:i')->placeholder('ვადა არ აქვს')->toggleable(),
            TextColumn::make('cost_price')->label('თვითღირებულება')->money('GEL')->sortable()->toggleable(),
            TextColumn::make('estimated_profit')
                ->label('მოგება / ცალი')
                ->state(fn (Product $record): ?float => $record->cost_price === null ? null : (float) $record->price - (float) $record->cost_price)
                ->money('GEL')
                ->color(fn (?float $state): string => $state === null ? 'gray' : ($state < 0 ? 'danger' : 'success'))
                ->placeholder('არ არის მითითებული')
                ->toggleable(),
            TextColumn::make('margin')
                ->label('მარჟა')
                ->state(fn (Product $record): ?string => ! $record->cost_price || (float) $record->price <= 0
                    ? null
                    : number_format((((float) $record->price - (float) $record->cost_price) / (float) $record->price) * 100, 1).' %')
                ->color('info')
                ->placeholder('—')
                ->toggleable(),
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
            Filter::make('negative_margin')
                ->label('ზარალზე გაყიდვა')
                ->query(fn (Builder $query): Builder => $query
                    ->whereNotNull('cost_price')
                    ->whereColumn('price', '<', 'cost_price')),
        ])->recordActions([
            Action::make('adjustStock')
                ->label('მარაგის მართვა')
                ->icon('heroicon-o-archive-box-arrow-down')
                ->color('info')
                ->schema([
                    Select::make('operation')
                        ->label('ოპერაცია')
                        ->options([
                            'restock' => 'მარაგის დამატება',
                            'remove' => 'მარაგის ჩამოწერა',
                            'set' => 'ნაშთის დაყენება',
                        ])
                        ->default('restock')
                        ->required()
                        ->native(false),
                    TextInput::make('quantity')
                        ->label(fn (callable $get): string => $get('operation') === 'set' ? 'ახალი ნაშთი' : 'რაოდენობა')
                        ->numeric()
                        ->integer()
                        ->minValue(0)
                        ->required(),
                    TextInput::make('reference')
                        ->label('დოკუმენტის ნომერი')
                        ->maxLength(100),
                    Textarea::make('note')
                        ->label('შენიშვნა')
                        ->rows(2)
                        ->maxLength(1000),
                ])
                ->action(function (Product $record, array $data): void {
                    DB::transaction(function () use ($record, $data): void {
                        $product = Product::query()->lockForUpdate()->findOrFail($record->getKey());
                        $before = (int) $product->stock;
                        $quantity = (int) $data['quantity'];

                        $after = match ($data['operation']) {
                            'restock' => $before + $quantity,
                            'remove' => $before - $quantity,
                            'set' => $quantity,
                            default => $before,
                        };

                        if ($after < 0) {
                            throw ValidationException::withMessages([
                                'quantity' => 'მარაგის ჩამოწერა არსებულ ნაშთზე მეტი ვერ იქნება.',
                            ]);
                        }

                        $delta = $after - $before;

                        if ($delta === 0) {
                            Notification::make()->title('მარაგი არ შეცვლილა')->info()->send();
                            return;
                        }

                        $product->update(['stock' => $after]);

                        InventoryMovement::create([
                            'product_id' => $product->id,
                            'user_id' => auth()->id(),
                            'movement_type' => $data['operation'] === 'restock' ? 'restock' : 'adjustment',
                            'quantity_change' => $delta,
                            'stock_before' => $before,
                            'stock_after' => $after,
                            'reference' => $data['reference'] ?? null,
                            'note' => $data['note'] ?? null,
                        ]);
                    });

                    Notification::make()->title('მარაგი წარმატებით განახლდა')->success()->send();
                }),
            \Filament\Actions\EditAction::make()->label('რედაქტირება'),
            \Filament\Actions\DeleteAction::make()->label('წაშლა'),
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
