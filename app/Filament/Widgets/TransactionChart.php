<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Support\Enums\FontWeight;
use Filament\Widgets\ChartWidget;

class TransactionChart extends ChartWidget
{
    protected static ?string $heading = 'Transaksi Bulanan';

    public ?string $filter = 'month';

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Hari ini',
            'week' => 'Minggu',
            'month' => 'Bulan',
            'year' => 'Tahun',
        ];
    }

    protected function getSummary(): ?string
    {
        $query = Transaction::query();

        $dateFrom = $this->getDateFrom();
        $dateTo = $this->getDateTo();

        if ($dateFrom && $dateTo) {
            $query->whereBetween('created_at', [$dateFrom, $dateTo]);
        } else {
            $this->applyDefaultFilter($query);
        }

        return number_format($query->count()) . ' transaksi';
    }

    protected function getDescriptionIcon(): ?string
    {
        return 'heroicon-o-arrow-trending-up';
    }

    protected function getDescriptionColor(): ?string
    {
        return 'success';
    }

    protected function getDescriptionFontWeight(): ?FontWeight
    {
        return FontWeight::Bold;
    }

    protected function getData(): array
    {
        $query = Transaction::query();
        $dateFrom = $this->getDateFrom();
        $dateTo = $this->getDateTo();

        if ($dateFrom && $dateTo) {
            $query->whereBetween('created_at', [$dateFrom, $dateTo]);

            $days = $dateFrom->diffInDays($dateTo) + 1;
            $labels = [];
            $data = [];

            for ($i = 0; $i < $days; $i++) {
                $date = $dateFrom->copy()->addDays($i);
                $labels[] = $date->translatedFormat('d M');
                $count = Transaction::whereDate('created_at', $date)->count();
                $data[] = $count;
            }

            return [
                'datasets' => [[
                    'label' => 'Jumlah Transaksi',
                    'data' => $data,
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.2)',
                    'fill' => true,
                    'tension' => 0.4,
                ]],
                'labels' => $labels,
            ];
        }

        // Jika tidak ada filter tanggal, pakai default
        $this->applyDefaultFilter($query);

        switch ($this->filter) {
            case 'today':
                $labels = range(0, 23);
                $labelNames = array_map(fn($h) => sprintf('%02d:00', $h), $labels);
                $data = $query->selectRaw("HOUR(created_at) as label, COUNT(*) as total")
                    ->groupBy('label')->pluck('total', 'label');
                break;

            case 'week':
                $labels = range(1, 7);
                $labelNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
                $data = $query->selectRaw("DAYOFWEEK(created_at) as label, COUNT(*) as total")
                    ->groupBy('label')->pluck('total', 'label')->all();
                break;

            case 'year':
                $labels = range(now()->year - 4, now()->year);
                $labelNames = $labels;
                $data = $query->selectRaw("YEAR(created_at) as label, COUNT(*) as total")
                    ->groupBy('label')->pluck('total', 'label');
                break;

            case 'month':
            default:
                $labels = range(1, 12);
                $labelNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                $data = $query->selectRaw("MONTH(created_at) as label, COUNT(*) as total")
                    ->groupBy('label')->pluck('total', 'label');
                break;
        }

        $chartData = [];
        foreach ($labels as $label) {
            $chartData[] = $data[$label] ?? 0;
        }

        return [
            'datasets' => [[
                'label' => 'Jumlah Transaksi',
                'data' => $chartData,
                'borderColor' => '#f59e0b',
                'backgroundColor' => 'rgba(245, 158, 11, 0.2)',
                'fill' => true,
                'tension' => 0.4,
            ]],
            'labels' => $labelNames,
        ];
    }

    protected function getType(): string
    {
        return 'line';
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

    private function applyDefaultFilter($query): void
    {
        $query
            ->when($this->filter === 'today', fn ($q) =>
                $q->whereDate('created_at', today()))
            ->when($this->filter === 'week', fn ($q) =>
                $q->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]))
            ->when($this->filter === 'month', fn ($q) =>
                $q->whereMonth('created_at', now()->month))
            ->when($this->filter === 'year', fn ($q) =>
                $q->whereYear('created_at', now()->year));
    }
}
