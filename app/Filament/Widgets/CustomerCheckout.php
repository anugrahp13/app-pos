<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Filament\Widgets\TableWidget as BaseWidget;
use Carbon\Carbon;

class CustomerCheckout extends BaseWidget
{
    protected static ?string $heading = 'Transaksi Pelanggan';

    protected function getTableQuery(): Builder
    {
        $dateFrom = $this->getDateFrom();
        $dateTo = $this->getDateTo();

        return Customer::query()
            ->whereHas('transactions', function ($query) use ($dateFrom, $dateTo) {
                if ($dateFrom && $dateTo) {
                    $query->whereBetween('created_at', [$dateFrom, $dateTo]);
                }
            })
            ->withCount([
                'transactions as transactions_count' => function ($query) use ($dateFrom, $dateTo) {
                    if ($dateFrom && $dateTo) {
                        $query->whereBetween('created_at', [$dateFrom, $dateTo]);
                    }
                },
            ])
            ->withSum([
                'transactions as transactions_total' => function ($query) use ($dateFrom, $dateTo) {
                    if ($dateFrom && $dateTo) {
                        $query->whereBetween('created_at', [$dateFrom, $dateTo]);
                    }
                },
            ], 'total')
            ->orderByDesc('transactions_total');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label('Pelanggan')
                ->searchable(),
            Tables\Columns\TextColumn::make('transactions_count')
                ->label('Total Transaksi')
                ->sortable(),
            Tables\Columns\TextColumn::make('transactions_total')
                ->label('Total Uang Transaksi')
                ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                ->sortable(),
        ];
    }

    protected function isTableSearchable(): bool
    {
        return true;
    }

    protected function getTableHeading(): ?string
    {
        $dateFrom = $this->getDateFrom()?->format('Y-m-d');
        $dateTo = $this->getDateTo()?->format('Y-m-d');

        if ($dateFrom && $dateTo) {
            return "Checkout Customer ($dateFrom sampai $dateTo)";
        }

        return 'Checkout Customer (Semua)';
    }

    private function getDateFrom(): ?Carbon
    {
        try {
            return session('filter_date_from')
                ? Carbon::parse(session('filter_date_from'))->startOfDay()
                : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function getDateTo(): ?Carbon
    {
        try {
            return session('filter_date_to')
                ? Carbon::parse(session('filter_date_to'))->endOfDay()
                : null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
