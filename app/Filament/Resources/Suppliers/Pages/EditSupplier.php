<?php

namespace App\Filament\Resources\Suppliers\Pages;

use App\Filament\Resources\Suppliers\SupplierResource;
use App\Models\Supplier;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSupplier extends EditRecord
{
    protected static string $resource = SupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('მომწოდებლის წაშლა')
                ->requiresConfirmation()
                ->visible(fn (Supplier $record): bool => ! $record->purchaseOrders()->exists() && ! $record->payments()->exists()),
        ];
    }
}
