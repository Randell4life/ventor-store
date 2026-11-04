<?php

namespace App\Filament\Resources\OperatingExpenses\Pages;

use App\Filament\Resources\OperatingExpenses\OperatingExpenseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOperatingExpense extends CreateRecord
{
    protected static string $resource = OperatingExpenseResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (($data['status'] ?? 'unpaid') === 'paid') {
            $data['paid_at'] ??= now()->toDateString();
        } else {
            $data['paid_at'] = null;
            $data['payment_method'] = null;
        }

        return $data;
    }
}
