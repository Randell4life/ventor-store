<?php

namespace App\Filament\Resources\OperatingExpenses\Pages;

use App\Filament\Resources\OperatingExpenses\OperatingExpenseResource;
use Filament\Resources\Pages\EditRecord;

class EditOperatingExpense extends EditRecord
{
    protected static string $resource = OperatingExpenseResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
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
