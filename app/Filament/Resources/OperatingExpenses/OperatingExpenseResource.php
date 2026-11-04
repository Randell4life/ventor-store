<?php

namespace App\Filament\Resources\OperatingExpenses;

use App\Filament\Resources\OperatingExpenses\Pages\CreateOperatingExpense;
use App\Filament\Resources\OperatingExpenses\Pages\EditOperatingExpense;
use App\Filament\Resources\OperatingExpenses\Pages\ListOperatingExpenses;
use App\Models\OperatingExpense;
use Filament\Forms\Components\DatePicker;
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

class OperatingExpenseResource extends Resource
{
    protected static ?string $model = OperatingExpense::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-receipt-percent';
    protected static ?string $navigationLabel = 'საოპერაციო ხარჯები';
    protected static ?string $modelLabel = 'ხარჯი';
    protected static ?string $pluralModelLabel = 'საოპერაციო ხარჯები';
    protected static string|\UnitEnum|null $navigationGroup = 'ფინანსები';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('category')
                ->label('ხარჯის კატეგორია')
                ->options(OperatingExpense::categories())
                ->required()
                ->native(false),
            TextInput::make('description')
                ->label('დანიშნულება / აღწერა')
                ->required()
                ->maxLength(255),
            TextInput::make('amount')
                ->label('თანხა (₾)')
                ->numeric()
                ->minValue(0.01)
                ->required(),
            DatePicker::make('expense_date')
                ->label('ხარჯის თარიღი')
                ->required()
                ->default(now())
                ->native(false),
            Select::make('status')
                ->label('გადახდის სტატუსი')
                ->options(['paid' => 'გადახდილი', 'unpaid' => 'გადასახდელი'])
                ->default('unpaid')
                ->required()
                ->native(false)
                ->live(),
            Select::make('payment_method')
                ->label('გადახდის მეთოდი')
                ->options(OperatingExpense::paymentMethods())
                ->required(fn (callable $get): bool => $get('status') === 'paid')
                ->native(false)
                ->visible(fn (callable $get): bool => $get('status') === 'paid'),
            DatePicker::make('paid_at')
                ->label('გადახდის თარიღი')
                ->native(false)
                ->visible(fn (callable $get): bool => $get('status') === 'paid')
                ->default(now()),
            TextInput::make('reference')
                ->label('ქვითრის / ტრანზაქციის ნომერი')
                ->maxLength(120),
            Textarea::make('notes')
                ->label('შენიშვნა')
                ->rows(3)
                ->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('expense_date')->label('თარიღი')->date('d.m.Y')->sortable(),
            TextColumn::make('category')->label('კატეგორია')
                ->formatStateUsing(fn (string $state): string => OperatingExpense::categories()[$state] ?? $state)
                ->badge(),
            TextColumn::make('description')->label('აღწერა')->searchable()->wrap(),
            TextColumn::make('amount')->label('თანხა')->money('GEL')->sortable(),
            TextColumn::make('status')->label('გადახდა')->badge()
                ->formatStateUsing(fn (string $state): string => $state === 'paid' ? 'გადახდილი' : 'გადასახდელი')
                ->color(fn (string $state): string => $state === 'paid' ? 'success' : 'warning'),
            TextColumn::make('payment_method')->label('მეთოდი')
                ->formatStateUsing(fn (?string $state): string => OperatingExpense::paymentMethods()[$state] ?? '—')
                ->toggleable(),
            TextColumn::make('paid_at')->label('გადახდის თარიღი')->date('d.m.Y')->placeholder('—')->toggleable(),
            TextColumn::make('creator.name')->label('ოპერატორი')->placeholder('სისტემა')->toggleable(),
        ])->filters([
            SelectFilter::make('category')->label('კატეგორია')->options(OperatingExpense::categories()),
            SelectFilter::make('status')->label('გადახდის სტატუსი')->options(['paid' => 'გადახდილი', 'unpaid' => 'გადასახდელი']),
            Filter::make('expense_date')
                ->form([
                    DatePicker::make('from')->label('დან')->native(false),
                    DatePicker::make('until')->label('მდე')->native(false),
                ])
                ->query(fn (Builder $query, array $data): Builder => $query
                    ->when($data['from'] ?? null, fn (Builder $q, $date): Builder => $q->whereDate('expense_date', '>=', $date))
                    ->when($data['until'] ?? null, fn (Builder $q, $date): Builder => $q->whereDate('expense_date', '<=', $date))),
        ])->defaultSort('expense_date', 'desc')->emptyStateHeading('ხარჯები ჯერ არ არის დამატებული');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOperatingExpenses::route('/'),
            'create' => CreateOperatingExpense::route('/create'),
            'edit' => EditOperatingExpense::route('/{record}/edit'),
        ];
    }
}
