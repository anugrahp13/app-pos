<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class BestSellingProducts extends BaseWidget
{
    protected static ?string $heading = 'Best Selling Products';

    protected function getTableQuery(): Builder
    {
        $dateFrom = $this->getDateFrom();
        $dateTo = $this->getDateTo();

        return Product::query()
            ->select('products.*')
            ->selectSub(function ($query) use ($dateFrom, $dateTo) {
                $query->from('transaction_items')
                    ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
                    ->whereColumn('transaction_items.product_id', 'products.id')
                    ->when($dateFrom && $dateTo, fn($q) => $q->whereBetween('transactions.created_at', [$dateFrom, $dateTo]))
                    ->selectRaw('COALESCE(SUM(transaction_items.quantity), 0)');
            }, 'total_sold')
            ->orderByDesc('total_sold');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\ImageColumn::make('image')
                ->label('Gambar')
                ->getStateUsing(fn ($record) => $record->image ?: 'products/default-image.png')
                ->disk('public'),

            Tables\Columns\TextColumn::make('name')
                ->label('Nama Produk')
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('total_sold')
                ->label('Terjual')
                ->sortable()
                ->badge()
                ->color(fn ($state) => $state == 0 ? 'gray' : 'success'),
        ];
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
