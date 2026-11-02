<?php

namespace App\Filament\Resources\Categories;

use App\Filament\Resources\Categories\Pages\CreateCategory;
use App\Filament\Resources\Categories\Pages\EditCategory;
use App\Filament\Resources\Categories\Pages\ListCategories;
use App\Models\Category;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static ?string $recordTitleAttribute = 'name';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationLabel = 'კატეგორიები';
    protected static ?string $modelLabel = 'კატეგორია';
    protected static ?string $pluralModelLabel = 'კატეგორიები';
    protected static string|\UnitEnum|null $navigationGroup = 'კატალოგი';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('სახელი')->required()->maxLength(120),
            TextInput::make('slug')->label('Slug')->helperText('ცარიელის დატოვებისას ავტომატურად შეიქმნება.')->maxLength(150),
            TextInput::make('sort_order')->label('რიგითობა')->numeric()->integer()->default(0),
            Textarea::make('description')->label('აღწერა')->rows(4)->columnSpanFull(),
            Toggle::make('is_active')->label('აქტიურია')->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('კატეგორია')->searchable()->sortable(),
            TextColumn::make('slug')->label('Slug')->searchable(),
            TextColumn::make('products_count')->counts('products')->label('პროდუქტები'),
            TextColumn::make('sort_order')->label('რიგი')->sortable(),
            IconColumn::make('is_active')->label('აქტიური')->boolean(),
        ])->recordActions([
            \Filament\Actions\EditAction::make()->label('რედაქტირება'),
            \Filament\Actions\DeleteAction::make()->label('წაშლა'),
        ])->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCategories::route('/'),
            'create' => CreateCategory::route('/create'),
            'edit' => EditCategory::route('/{record}/edit'),
        ];
    }
}
