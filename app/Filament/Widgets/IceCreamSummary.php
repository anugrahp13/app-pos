<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class IceCreamSummary extends BaseWidget
{
    protected function getCards(): array
    {
        $iceCreamTotal = Transaction::with('items.product.category')
            ->get()
            ->flatMap->items
            ->filter(fn ($item) => $item->product->category->name === 'Ice Cream')
            ->sum(fn ($item) => $item->quantity * $item->price);

        return [
            Card::make('Total Pendapatan Ice Cream', 'Rp ' . number_format($iceCreamTotal, 0, ',', '.')),
        ];
    }
}
