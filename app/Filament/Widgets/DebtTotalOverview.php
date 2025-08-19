<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Facades\DB;

class DebtTotalOverview extends BaseWidget
{
    protected function getCards(): array
    {
        $totalPiutang = Transaction::where('is_debt', true)
            ->whereIn('status', ['unpaid', 'partial'])
            ->sum(DB::raw('total - initial_payment'));

        $totalCustomer = Transaction::where('is_debt', true)
            ->whereIn('status', ['unpaid', 'partial'])
            ->distinct('customer_id')
            ->count();

        // Tentukan warna seluruh Card berdasarkan nilai
        $color = 'success';
        if ($totalPiutang > 100_000) $color = 'warning';
        if ($totalPiutang > 500_000) $color = 'danger';

        return [
            Card::make('Total Piutang Aktif', 'Rp ' . number_format($totalPiutang, 0, ',', '.'))
                ->description("Dari {$totalCustomer} Customer")
                ->color($color)
                ->icon('heroicon-o-banknotes'),
        ];
    }
}
