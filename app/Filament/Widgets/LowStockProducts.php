<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LowStockProducts extends BaseWidget
{
    protected static ?string $heading = 'Stok Produk';

    // Ambil data produk dengan stok terendah
    protected function getTableQuery(): Builder
    {
        return Product::query()
            ->orderBy('stock', 'asc')
            ->limit(5); // hanya tampilkan 10 produk
    }

    // Tampilkan kolom produk dan badge warna pada kolom stok
    protected function getTableColumns(): array
    {
        return [
            ImageColumn::make('image')
                ->label('Gambar')
                ->getStateUsing(fn ($record) => $record->image ?: 'products/default-image.png')
                ->disk('public'),
            Tables\Columns\TextColumn::make('name')
                ->label('Nama Produk')
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('stock')
                ->label('Stok')
                ->badge()
                ->color(fn (int $state): string => match (true) {
                    $state < 5 => 'danger',      // Merah
                    $state < 11 => 'warning',    // Kuning
                    default => 'success',        // Hijau
                }),
        ];
    }
}
