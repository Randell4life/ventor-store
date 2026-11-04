<?php

namespace App\Filament\Resources\PurchaseOrders\Pages;

use App\Filament\Resources\PurchaseOrders\PurchaseOrderResource;
use App\Models\PurchaseOrder;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPurchaseOrder extends EditRecord
{
    protected static string $resource = PurchaseOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('მონახაზის წაშლა')
                ->requiresConfirmation()
                ->visible(fn (PurchaseOrder $record): bool => $record->status === 'draft' && ! $record->payments()->exists()),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['order_number'], $data['subtotal'], $data['received_at']);

        return $data;
    }
}
