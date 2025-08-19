<?php

namespace App\Filament\Resources\ProductResource\Widgets;

use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProductStats extends BaseWidget
{
    protected function getStats(): array
    {
        $totalProducts = Product::count();
        $activeProduct = Product::where('status', 'active')->count();
        $inactiveProduct = Product::where('status', 'inactive')->count();
        $lowStockProducts = Product::where('stock', '<', 3)->count();
        return [
            Stat::make('Total Products', $totalProducts)
                ->icon('heroicon-o-squares-2x2'),
            Stat::make('Active Products', $activeProduct)
                ->icon('heroicon-o-check-badge'),
            Stat::make('Inactive Products', $inactiveProduct)
                ->icon('heroicon-o-x-circle'),
            Stat::make('Low Stock (< 3)', $lowStockProducts)
                ->icon('heroicon-o-exclamation-circle'),

        ];
    }
}
