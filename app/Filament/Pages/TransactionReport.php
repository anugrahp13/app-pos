<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\IceCreamSummary;
use App\Models\Category;
use App\Models\Transaction;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class TransactionReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationLabel = 'Laporan Transaksi';
    protected static ?string $slug = 'transaction-report';
    protected static bool $shouldRegisterNavigation = false;
    protected static string $view = 'filament.pages.transaction-report';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Transaction::query()->with(['customer', 'user'])
            )
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Pelanggan')
                    ->searchable(),
        
                Tables\Columns\TextColumn::make('gross_total')
                    ->label('Pendapatan Kotor')
                    ->state(function ($record) {
                        return $record->items->sum(fn ($item) => $item->quantity * $item->price);
                    })
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable(),
            
                Tables\Columns\TextColumn::make('net_profit')
                    ->label('Pendapatan Bersih')
                    ->state(function ($record) {
                        return $record->items->sum(fn ($item) => $item->quantity * ($item->price - $item->product->purchase_price));
                    })
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('total_items')
                    ->label('Jumlah Barang')
                    ->state(function ($record) {
                        return $record->items->sum('quantity') . ' pcs';
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('paid')
                    ->label('Bayar (Cash)')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state ?? 0, 0, ',', '.'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('change_amount')
                    ->label('Kembalian')
                    ->state(fn ($record) => max(($record->paid ?? 0) - ($record->total ?? 0), 0))
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Dibuat oleh')
                    ->searchable()
                    ->sortable(),
                BadgeColumn::make('status')
                    ->colors([
                        'danger' => fn ($state) => $state === 'unpaid',
                        'warning' => fn ($state) => $state === 'partial',
                        'success' => fn ($state) => $state === 'paid',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'unpaid' => 'Belum Bayar',
                        'partial' => 'Sebagian',
                        'paid' => 'Lunas',
                        default => ucfirst($state),
                    })
                    ->searchable()
                    ->sortable(),
                            
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->state(fn ($record) => $record->created_at->format('d M Y H:i'))
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from')->label('Dari'),
                        \Filament\Forms\Components\DatePicker::make('to')->label('Sampai'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['from'], fn ($q) => $q->whereDate('created_at', '>=', $data['from']))
                            ->when($data['to'], fn ($q) => $q->whereDate('created_at', '<=', $data['to']));
                    }),
            ])
            ->bulkActions([
                ExportBulkAction::make('export_excel')
            ]);                     
    }
}

