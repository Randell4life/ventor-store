<?php

namespace App\\Filament\\Resources\\Orders;

use App\\Filament\\Resources\\Orders\\Pages\\EditOrder;
use App\\Filament\\Resources\\Orders\\Pages\\ListOrders;
use App\\Models\\Order;
use Filament\\Forms\\Components\\Select;
use Filament\\Forms\\Components\\Textarea;
use Filament\\Forms\\Components\\TextInput;
use Filament\\Resources\\Resource;
use Filament\\Schemas\\Schema;
use Filament\\Tables\\Columns\\TextColumn;
use Filament\\Tables\\Filters\\SelectFilter;
use Filament\\Tables\\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $recordTitleAttribute = 'order_number';
    protected static string|\\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationLabel = 'შეკვეთები';
    protected static ?string $modelLabel = 'შეკვეთა';
    protected static ?string $pluralModelLabel = 'შეკვეთები';
    protected static string|\\UnitEnum|null $navigationGroup = 'გაყიდვები';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('order_number')->label('შეკვეთის ნომერი')->disabled(),
            TextInput::make('customer_name')->label('მომხმარებელი')->disabled(),
            TextInput::make('phone')->label('ტელეფონი')->tel()->disabled(),
            TextInput::make('email')->label('ელფოსტა')->disabled(),
            TextInput::make('city')->label('ქალაქი')->disabled(),
            TextInput::make('address')->label('მისამართი')->disabled(),
            TextInput::make('total')->label('ჯამი (₾)')->disabled(),
            Select::make('status')->label('სტატუსი')->options([
                'new' => 'ახალი',
                'confirmed' => 'დადასტურებული',
                'processing' => 'მუშავდება',
                'shipped' => 'გაგზავნილია',
                'completed' => 'დასრულებული',
                'cancelled' => 'გაუქმებული',
            ])->required(),
            Select::make('payment_method')->label('გადახდის მეთოდი')->options([
                'cash_on_delivery' => 'კურიერთან ნაღდი ანგარიშსწორება',
                'bank_transfer' => 'საბანკო გადარიცხვა',
            ])->disabled(),
            Textarea::make('note')->label('კომენტარი')->disabled()->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('order_number')->label('შეკვეთა')->searchable()->sortable()->weight('bold'),
            TextColumn::make('customer_name')->label('მომხმარებელი')->searchable(),
            TextColumn::make('phone')->label('ტელეფონი')->searchable(),
            TextColumn::make('email')->label('ელფოსტა')->searchable()->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('city')->label('ქალაქი')->searchable(),
            TextColumn::make('items_count')->counts('items')->label('პოზიციები'),
            TextColumn::make('total')->label('ჯამი')->money('GEL')->sortable(),
            TextColumn::make('status')->label('სტატუსი')->badge()->sortable()
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
            TextColumn::make('created_at')->label('თარიღი')->dateTime('d.m.Y H:i')->sortable(),
        ])->filters([
            SelectFilter::make('status')->label('სტატუსი')->options([
                'new' => 'ახალი',
                'confirmed' => 'დადასტურებული',
                'processing' => 'მუშავდება',
                'shipped' => 'გაგზავნილია',
                'completed' => 'დასრულებული',
                'cancelled' => 'გაუქმებული',
            ]),
        ])->recordActions([
            \\Filament\\Actions\\EditAction::make()->label('ნახვა / სტატუსის შეცვლა'),
        ])->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'edit' => EditOrder::route('/{record}/edit'),
        ];
    }
}
