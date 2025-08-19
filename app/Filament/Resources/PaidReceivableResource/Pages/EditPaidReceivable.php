<?php

namespace App\Filament\Resources\PaidReceivableResource\Pages;

use App\Filament\Resources\PaidReceivableResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPaidReceivable extends EditRecord
{
    protected static string $resource = PaidReceivableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
