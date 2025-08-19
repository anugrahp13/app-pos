<?php

namespace App\Filament\Widgets;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardOverview extends BaseWidget
{
    protected function getStats(): array
    {
        try {
            $dateFrom = session('filter_date_from')
                ? Carbon::parse(session('filter_date_from'))->startOfDay()
                : now()->startOfMonth();

            $dateTo = session('filter_date_to')
                ? Carbon::parse(session('filter_date_to'))->endOfDay()
                : now()->endOfMonth();
        } catch (\Exception $e) {
            $dateFrom = now()->startOfMonth();
            $dateTo = now()->endOfMonth();
        }

        // Query dasar
        $query = DB::table('transaction_items')
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->whereBetween('transactions.created_at', [$dateFrom, $dateTo]);

        // Ambil ID kategori Ice Cream
        $iceCreamCategoryId = DB::table('categories')->where('name', 'Ice Cream')->value('id');
        // Ambil ID kategori Rokok
        $cigaretteCategoryId = DB::table('categories')->where('name', 'Rokok')->value('id');

        // Total Pendapatan Kotor (hanya produk non Ice Cream dan non Rokok)
        $grossRevenue = (clone $query)
        ->whereNotIn('products.category_id', [$iceCreamCategoryId, $cigaretteCategoryId])
        ->selectRaw('SUM(transaction_items.quantity * transaction_items.price) as total')
        ->value('total') ?? 0;

        // Total Keuntungan Bersih (hanya produk non Ice Cream dan non Rokok)
        $netProfit = (clone $query)
        ->whereNotIn('products.category_id', [$iceCreamCategoryId, $cigaretteCategoryId])
        ->selectRaw('SUM(transaction_items.quantity * (transaction_items.price - products.purchase_price)) as profit')
        ->value('profit') ?? 0;

        // Total Order (jumlah transaksi unik)
        $orderCount = DB::table('transactions')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->count();
        
        // Pendapatan Kotor Ice Cream
        $iceCreamGross = (clone $query)
            ->where('products.category_id', $iceCreamCategoryId)
            ->selectRaw('SUM(transaction_items.quantity * transaction_items.price) as total')
            ->value('total') ?? 0;

        // Keuntungan Bersih Ice Cream
        $iceCreamNet = (clone $query)
            ->where('products.category_id', $iceCreamCategoryId)
            ->selectRaw('SUM(transaction_items.quantity * (transaction_items.price - products.purchase_price)) as profit')
            ->value('profit') ?? 0;

        // // Hitung ulang Pendapatan & Keuntungan tanpa Ice Cream
        // $grossRevenue -= $iceCreamGross;
        // $netProfit -= $iceCreamNet;

        // Pendapatan Kotor Rokok
        $cigaretteGross = (clone $query)
            ->where('products.category_id', $cigaretteCategoryId)
            ->selectRaw('SUM(transaction_items.quantity * transaction_items.price) as total')
            ->value('total') ?? 0;

        // Keuntungan Bersih Rokok
        $cigaretteNet = (clone $query)
            ->where('products.category_id', $cigaretteCategoryId)
            ->selectRaw('SUM(transaction_items.quantity * (transaction_items.price - products.purchase_price)) as profit')
            ->value('profit') ?? 0;


        // Data grafik harian
        $dailyChartData = (clone $query)
            ->selectRaw('DATE(transactions.created_at) as day, SUM(transaction_items.quantity * transaction_items.price) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day');

        $days = [];
        $chart = [];

        $period = Carbon::parse($dateFrom)->daysUntil(Carbon::parse($dateTo));
        foreach ($period as $day) {
            $dayStr = $day->toDateString();
            $days[] = $day->translatedFormat('d M');
            $chart[] = $dailyChartData[$dayStr] ?? 0;
        }

        return [
            Stat::make('Pendapatan Kotor', 'Rp ' . number_format($grossRevenue, 0, ',', '.'))
                ->description('Total semua penjualan')
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->color('warning')
                ->chart($chart)
                ->icon('heroicon-o-banknotes'),

            Stat::make('Pendapatan Bersih', 'Rp ' . number_format($netProfit, 0, ',', '.'))
                ->description('Keuntungan setelah dikurangi modal')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('success')
                ->chart($chart)
                ->icon('heroicon-o-currency-dollar'),
            
            Stat::make('Penjualan Ice Cream', 'Rp ' . number_format($iceCreamGross, 0, ',', '.'))
                ->description('Pendapatan kotor hanya produk Ice Cream')
                ->descriptionIcon('heroicon-o-cube')
                ->color('warning')
                ->chart($chart)
                ->icon('heroicon-o-cube'),

            Stat::make('Pendapatan Bersih Ice Cream', 'Rp ' . number_format($iceCreamNet, 0, ',', '.'))
                ->description('Keuntungan bersih hanya produk Ice Cream')
                ->descriptionIcon('heroicon-o-chart-bar')
                ->color('success')
                ->chart($chart)
                ->icon('heroicon-o-chart-bar'),
            
            Stat::make('Penjualan Rokok', 'Rp ' . number_format($cigaretteGross, 0, ',', '.'))
                ->description('Pendapatan kotor hanya produk Rokok')
                ->descriptionIcon('heroicon-o-fire')
                ->color('warning')
                ->chart($chart)
                ->icon('heroicon-o-fire'),
            
            Stat::make('Pendapatan Bersih Rokok', 'Rp ' . number_format($cigaretteNet, 0, ',', '.'))
                ->description('Keuntungan bersih hanya produk Rokok')
                ->descriptionIcon('heroicon-o-chart-bar')
                ->color('success')
                ->chart($chart)
                ->icon('heroicon-o-chart-bar'),   
            
            Stat::make('Jumlah Order', number_format($orderCount) . ' Transaksi')
                ->description("Jumlah transaksi yang terjadi")
                ->descriptionIcon('heroicon-o-shopping-bag')
                ->color('info')
                ->chart($chart)
                ->icon('heroicon-o-shopping-bag'),
        ];
    }
}
