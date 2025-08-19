<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('goToPOS')
                ->label('Buat Transaksi')
                ->url('/transaction') // relatif ke domain, atau gunakan url lengkap jika perlu
                ->icon('heroicon-o-plus-circle')
                ->color('primary')
                ->openUrlInNewTab(), // jika ingin dibuka di tab baru (opsional)
        ];
    }
}
