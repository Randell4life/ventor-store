<?php

namespace App\Filament\Resources\Orders;

use App\Filament\Resources\Orders\Pages\EditOrder;
use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Models\Order;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $recordTitleAttribute = 'order_number';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationLabel = 'შეკვეთები';
    protected static ?string $modelLabel = 'შეკვეთა';
    protected static ?string $pluralModelLabel = 'შეკვეთები';
    protected static string|\UnitEnum|null $navigationGroup = 'გაყიდვები';
    protected static ?int $navigationSort = 1;

    private static function statusOptions(): array
    {
        return [
            'new' => 'ახალი',
            'confirmed' => 'დადასტურებული',
            'processing' => 'მუშავდება',
            'shipped' => 'გაგზავნილია',
            'completed' => 'დასრულებული',
            'cancelled' => 'გაუქმებული',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('order_number')->label('შეკვეთის ნომერი')->disabled(),
            TextInput::make('customer_name')->label('მომხმარებელი')->disabled(),
            TextInput::make('phone')->label('ტელეფონი')->tel()->disabled()->copyable(),
            TextInput::make('email')->label('ელფოსტა')->disabled()->copyable(),
            TextInput::make('city')->label('ქალაქი')->disabled(),
            TextInput::make('address')->label('მისამართი')->disabled()->columnSpanFull(),
            TextInput::make('subtotal')->label('პროდუქტების ჯამი (₾)')->disabled(),
            TextInput::make('delivery_fee')->label('მიწოდება (₾)')->disabled(),
            TextInput::make('total')->label('სულ გადასახდელი (₾)')->disabled(),
            Repeater::make('items')
                ->label('შეკვეთის პროდუქტები')
                ->relationship()
                ->schema([
                    TextInput::make('product_name')->label('პროდუქტი')->disabled(),
                    TextInput::make('sku')->label('SKU')->disabled(),
                    TextInput::make('quantity')->label('რაოდენობა')->numeric()->disabled(),
                    TextInput::make('unit_price')->label('ერთეულის ფასი (₾)')->numeric()->disabled(),
                    TextInput::make('line_total')->label('პოზიციის ჯამი (₾)')->numeric()->disabled(),
                ])
                ->columns(5)
                ->disabled()
                ->columnSpanFull(),
            Select::make('status')
                ->label('შეკვეთის სტატუსი')
                ->options(self::statusOptions())
                ->required()
                ->native(false)
                ->helperText('სტატუსის ცვლილება შეინახება გვერდის შენახვისას.'),
            Select::make('payment_method')->label('გადახდის მეთოდი')->options([
                'cash_on_delivery' => 'კურიერთან ნაღდი ანგარიშსწორება',
                'bank_transfer' => 'საბანკო გადარიცხვა',
            ])->disabled(),
            Textarea::make('note')->label('მომხმარებლის კომენტარი')->disabled()->columnSpanFull(),
            TextInput::make('created_at')->label('შეკვეთის თარიღი')->disabled(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->label('შეკვეთა')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),
                TextColumn::make('customer_name')->label('მომხმარებელი')->searchable(),
                TextColumn::make('phone')->label('ტელეფონი')->searchable()->copyable(),
                TextColumn::make('email')->label('ელფოსტა')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('city')->label('ქალაქი')->searchable()->toggleable(),
                TextColumn::make('items_count')->counts('items')->label('პოზიციები')->sortable(),
                TextColumn::make('total')->label('ჯამი')->money('GEL')->sortable()->weight('bold'),
                TextColumn::make('payment_method')
                    ->label('გადახდა')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'cash_on_delivery' => 'კურიერთან',
                        'bank_transfer' => 'გადარიცხვა',
                        default => $state ?? '—',
                    })
                    ->color('gray')
                    ->toggleable(),
                TextColumn::make('status')
                    ->label('სტატუსი')
                    ->badge()
                    ->sortable()
                    ->formatStateUsing(fn (string $state): string => self::statusOptions()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'warning',
                        'confirmed' => 'info',
                        'processing' => 'primary',
                        'shipped', 'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')->label('თარიღი')->dateTime('d.m.Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label('სტატუსი')->options(self::statusOptions()),
                SelectFilter::make('payment_method')->label('გადახდის მეთოდი')->options([
                    'cash_on_delivery' => 'კურიერთან ნაღდი ანგარიშსწორება',
                    'bank_transfer' => 'საბანკო გადარიცხვა',
                ]),
                Filter::make('created_at')
                    ->label('შეკვეთის პერიოდი')
                    ->schema([
                        DatePicker::make('from')->label('დან')->native(false),
                        DatePicker::make('until')->label('მდე')->native(false),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'] ?? null, fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date))
                        ->when($data['until'] ?? null, fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date))),
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make()->label('დეტალები / სტატუსი'),
            ])
            ->recordUrl(fn (Order $record): string => EditOrder::getUrl(['record' => $record]))
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('შეკვეთები ჯერ არ არის')
            ->emptyStateDescription('ახალი შეკვეთები ავტომატურად გამოჩნდება ამ სიაში.');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'edit' => EditOrder::route('/{record}/edit'),
        ];
    }
}
