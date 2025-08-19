<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class CigaretteSummary extends BaseWidget
{
    protected function getCards(): array
    {
        $cigaretteTotal = Transaction::with('items.product.category')
            ->get()
            ->flatMap->items
            ->filter(fn ($item) => $item->product->category->name === 'Rokok')
            ->sum(fn ($item) => $item->quantity * $item->price);

        return [
            Card::make('Total Pendapatan Rokok', 'Rp ' . number_format($cigaretteTotal, 0, ',', '.')),
        ];
    }
}
