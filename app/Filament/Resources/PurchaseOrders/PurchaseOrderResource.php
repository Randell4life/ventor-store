<?php

namespace App\Filament\Resources\PurchaseOrders;

use App\Filament\Resources\PurchaseOrders\Pages\CreatePurchaseOrder;
use App\Filament\Resources\PurchaseOrders\Pages\EditPurchaseOrder;
use App\Filament\Resources\PurchaseOrders\Pages\ListPurchaseOrders;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\PurchaseOrder;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PurchaseOrderResource extends Resource
{
    protected static ?string $model = PurchaseOrder::class;
    protected static ?string $recordTitleAttribute = 'order_number';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'შესყიდვის დოკუმენტები';
    protected static ?string $modelLabel = 'შესყიდვის დოკუმენტი';
    protected static ?string $pluralModelLabel = 'შესყიდვის დოკუმენტები';
    protected static string|\UnitEnum|null $navigationGroup = 'შესყიდვები';
    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withSum('payments', 'amount');
    }

    private static function statuses(): array
    {
        return [
            'draft' => 'მონახაზი',
            'ordered' => 'შეკვეთილია',
            'received' => 'მიღებულია',
            'cancelled' => 'გაუქმებულია',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('supplier_id')
                ->label('მომწოდებელი')
                ->relationship('supplier', 'name', modifyQueryUsing: fn ($query) => $query->where('is_active', true))
                ->searchable()->preload()->required()->disabled(fn (?PurchaseOrder $record): bool => $record?->status === 'received'),
            TextInput::make('order_number')->label('შესყიდვის ნომერი')->disabled()->dehydrated(false),
            Select::make('status')->label('სტატუსი')->options(self::statuses())->default('draft')->required()->native(false)
                ->disabled(fn (?PurchaseOrder $record): bool => $record?->status === 'received'),
            Repeater::make('items')
                ->label('პროდუქტები')
                ->relationship()
                ->schema([
                    Select::make('product_id')->label('პროდუქტი')
                        ->relationship('product', 'name')
                        ->searchable()->preload()->required()
                        ->disabled(fn (?PurchaseOrder $record): bool => $record?->status === 'received'),
                    TextInput::make('quantity')->label('რაოდენობა')->numeric()->integer()->minValue(1)->required()
                        ->disabled(fn (?PurchaseOrder $record): bool => $record?->status === 'received'),
                    TextInput::make('unit_cost')->label('შესყიდვის ფასი / ერთეული (₾)')->numeric()->minValue(0)->required()
                        ->disabled(fn (?PurchaseOrder $record): bool => $record?->status === 'received'),
                ])->columns(3)->minItems(1)->required()->columnSpanFull(),
            Textarea::make('notes')->label('შენიშვნა')->rows(3)->columnSpanFull()
                ->disabled(fn (?PurchaseOrder $record): bool => $record?->status === 'received'),
            TextInput::make('subtotal')->label('ჯამი (₾)')->disabled()->dehydrated(false),
            TextInput::make('received_at')->label('მიღების თარიღი')->disabled()->dehydrated(false),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('order_number')->label('დოკუმენტი')->searchable()->sortable()->copyable()->weight('bold'),
            TextColumn::make('supplier.name')->label('მომწოდებელი')->searchable()->sortable(),
            TextColumn::make('items_count')->counts('items')->label('პოზიციები')->sortable(),
            TextColumn::make('subtotal')->label('ჯამი')->money('GEL')->sortable(),
            TextColumn::make('paid_amount')->label('გადახდილია')->state(fn (PurchaseOrder $record): float => $record->paidAmount())->money('GEL'),
            TextColumn::make('balance_due')->label('დავალიანება')->state(fn (PurchaseOrder $record): float => $record->balanceDue())->money('GEL')
                ->color(fn (PurchaseOrder $record): string => $record->balanceDue() > 0 ? 'danger' : 'success'),
            TextColumn::make('payment_status')->label('გადახდის სტატუსი')->badge()
                ->state(fn (PurchaseOrder $record): string => match ($record->paymentStatus()) {
                    'paid' => 'სრულად გადახდილი',
                    'partial' => 'ნაწილობრივ გადახდილი',
                    default => 'გადაუხდელი',
                })
                ->color(fn (PurchaseOrder $record): string => match ($record->paymentStatus()) {
                    'paid' => 'success',
                    'partial' => 'warning',
                    default => 'danger',
                }),
            TextColumn::make('status')->label('სტატუსი')->badge()
                ->formatStateUsing(fn (string $state): string => self::statuses()[$state] ?? $state)
                ->color(fn (string $state): string => match ($state) {
                    'draft' => 'gray',
                    'ordered' => 'warning',
                    'received' => 'success',
                    'cancelled' => 'danger',
                    default => 'gray',
                }),
            TextColumn::make('created_at')->label('შექმნის თარიღი')->dateTime('d.m.Y H:i')->sortable(),
            TextColumn::make('received_at')->label('მიღების თარიღი')->dateTime('d.m.Y H:i')->placeholder('—')->toggleable(),
        ])->filters([
            SelectFilter::make('status')->label('სტატუსი')->options(self::statuses()),
            SelectFilter::make('supplier')->label('მომწოდებელი')->relationship('supplier', 'name'),
        ])->recordActions([
            Action::make('receive')
                ->label('საქონლის მიღება')
                ->icon('heroicon-o-inbox-arrow-down')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('შესყიდვის მიღება')
                ->modalDescription('დადასტურების შემდეგ რაოდენობები დაემატება საწყობს, განახლდება საშუალო თვითღირებულება და ჩაიწერება მარაგის მოძრაობა. ეს ოპერაცია უკან აღარ ბრუნდება.')
                ->visible(fn (PurchaseOrder $record): bool => in_array($record->status, ['draft', 'ordered'], true))
                ->action(function (PurchaseOrder $record): void {
                    DB::transaction(function () use ($record): void {
                        $order = PurchaseOrder::query()->lockForUpdate()->with('items')->findOrFail($record->getKey());

                        if (! in_array($order->status, ['draft', 'ordered'], true)) {
                            throw ValidationException::withMessages(['status' => 'ეს შესყიდვა უკვე მიღებულია ან გაუქმებულია.']);
                        }

                        if ($order->items->isEmpty()) {
                            throw ValidationException::withMessages(['items' => 'შესყიდვის დოკუმენტში პროდუქტები არ არის.']);
                        }

                        foreach ($order->items as $item) {
                            if (! $item->product_id) {
                                throw ValidationException::withMessages(['items' => 'ერთ-ერთი პროდუქტი წაშლილია. დოკუმენტი ვერ მიიღება.']);
                            }

                            $product = Product::query()->lockForUpdate()->find($item->product_id);

                            if (! $product) {
                                throw ValidationException::withMessages(['items' => 'შესყიდვის პროდუქტებიდან ერთ-ერთი აღარ არსებობს.']);
                            }

                            $before = (int) $product->stock;
                            $receivedQuantity = (int) $item->quantity;
                            $after = $before + $receivedQuantity;
                            $oldCost = (float) ($product->cost_price ?? 0);
                            $newCost = (float) $item->unit_cost;
                            $weightedCost = $product->cost_price === null || $before === 0
                                ? $newCost
                                : (($before * $oldCost) + ($receivedQuantity * $newCost)) / $after;

                            $product->update([
                                'stock' => $after,
                                'cost_price' => round($weightedCost, 2),
                            ]);

                            InventoryMovement::create([
                                'product_id' => $product->id,
                                'user_id' => auth()->id(),
                                'movement_type' => 'restock',
                                'quantity_change' => $receivedQuantity,
                                'stock_before' => $before,
                                'stock_after' => $after,
                                'reference' => $order->order_number,
                                'note' => 'საქონლის მიღება მომწოდებლისგან: '.$order->supplier->name,
                            ]);
                        }

                        $order->update([
                            'status' => 'received',
                            'received_at' => now(),
                        ]);
                    });

                    Notification::make()->title('საქონელი მიღებულია')->body('მარაგი, თვითღირებულება და მოძრაობების ისტორია განახლდა.')->success()->send();
                }),
            Action::make('recordPayment')
                ->label('გადახდის დაფიქსირება')
                ->icon('heroicon-o-banknotes')
                ->color('warning')
                ->visible(fn (PurchaseOrder $record): bool => $record->status === 'received' && $record->balanceDue() > 0)
                ->form([
                    TextInput::make('amount')->label('გადახდის თანხა (₾)')->numeric()->minValue(0.01)->required(),
                    Select::make('payment_method')->label('გადახდის მეთოდი')->options([
                        'bank_transfer' => 'საბანკო გადარიცხვა',
                        'cash' => 'ნაღდი ფული',
                        'card' => 'ბარათი',
                        'other' => 'სხვა',
                    ])->default('bank_transfer')->required()->native(false),
                    TextInput::make('reference')->label('ტრანზაქციის / ქვითრის ნომერი')->maxLength(120),
                    Textarea::make('note')->label('შენიშვნა')->rows(2),
                ])
                ->action(function (PurchaseOrder $record, array $data): void {
                    $record->recordPayment(
                        (float) $data['amount'],
                        $data['payment_method'],
                        $data['reference'] ?? null,
                        $data['note'] ?? null,
                    );

                    Notification::make()->title('გადახდა დაფიქსირდა')->success()->send();
                }),
            Action::make('paymentHistory')
                ->label('გადახდების ისტორია')
                ->icon('heroicon-o-clock')
                ->modalHeading('მომწოდებლის გადახდები')
                ->modalContent(fn (PurchaseOrder $record) => view('filament.purchase-orders.payment-history', [
                    'payments' => $record->payments()->with('user')->latest('paid_at')->get(),
                ]))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('დახურვა')
                ->visible(fn (PurchaseOrder $record): bool => $record->payments()->exists()),
            \Filament\Actions\EditAction::make()->label('დეტალები / რედაქტირება'),
        ])->defaultSort('created_at', 'desc')->emptyStateHeading('შესყიდვის დოკუმენტები ჯერ არ არის');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPurchaseOrders::route('/'),
            'create' => CreatePurchaseOrder::route('/create'),
            'edit' => EditPurchaseOrder::route('/{record}/edit'),
        ];
    }
}
