<?php

namespace App\Filament\Resources\AccountsReceivableResource\Pages;

use App\Filament\Resources\AccountsReceivableResource;
use App\Filament\Widgets\DebtTotalOverview;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Filters\TabsFilter;
use Filament\Tables\Filters\TabsFilter\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListAccountsReceivables extends ListRecords
{
    protected static string $resource = AccountsReceivableResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            DebtTotalOverview::class,
        ];
    }
}
