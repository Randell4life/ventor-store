<?php

namespace App\Filament\Resources\Coupons;

use App\Filament\Resources\Coupons\Pages\CreateCoupon;
use App\Filament\Resources\Coupons\Pages\EditCoupon;
use App\Filament\Resources\Coupons\Pages\ListCoupons;
use App\Models\Coupon;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationLabel = 'პრომოკოდები';
    protected static ?string $modelLabel = 'პრომოკოდი';
    protected static ?string $pluralModelLabel = 'პრომოკოდები';
    protected static string|\UnitEnum|null $navigationGroup = 'მარკეტინგი';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('code')->label('კოდი')->required()->maxLength(50)->unique(ignoreRecord: true)->dehydrateStateUsing(fn ($state) => strtoupper(trim($state))),
            Select::make('type')->label('ტიპი')->options(['percent' => 'პროცენტული', 'fixed' => 'ფიქსირებული თანხა'])->required()->default('percent')->native(false),
            TextInput::make('value')->label('ფასდაკლება')->numeric()->minValue(0.01)->required(),
            TextInput::make('minimum_order')->label('მინიმალური შეკვეთა (₾)')->numeric()->minValue(0)->default(0),
            TextInput::make('maximum_discount')->label('მაქსიმალური ფასდაკლება (₾)')->numeric()->minValue(0)->nullable(),
            TextInput::make('usage_limit')->label('გამოყენების ლიმიტი')->numeric()->integer()->minValue(1)->nullable(),
            DateTimePicker::make('starts_at')->label('მოქმედების დაწყება')->seconds(false)->native(false),
            DateTimePicker::make('ends_at')->label('მოქმედების დასრულება')->seconds(false)->native(false)->after('starts_at'),
            Toggle::make('is_active')->label('აქტიურია')->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('code')->label('კოდი')->searchable()->sortable()->weight('bold'),
            TextColumn::make('type')->label('ტიპი')->formatStateUsing(fn ($state) => $state === 'fixed' ? 'ფიქსირებული' : 'პროცენტული'),
            TextColumn::make('value')->label('მნიშვნელობა')->numeric(2),
            TextColumn::make('used_count')->label('გამოყენებულია')->numeric(),
            TextColumn::make('usage_limit')->label('ლიმიტი')->placeholder('შეუზღუდავი'),
            TextColumn::make('ends_at')->label('ვადა')->dateTime('d.m.Y H:i')->placeholder('უვადო'),
            IconColumn::make('is_active')->label('აქტიური')->boolean(),
        ])->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCoupons::route('/'),
            'create' => CreateCoupon::route('/create'),
            'edit' => EditCoupon::route('/{record}/edit'),
        ];
    }
}
