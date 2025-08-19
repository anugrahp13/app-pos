<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Livewire\SendStruk;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Filament\Support\Facades\Livewire;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Transaction';
    protected static ?string $navigationGroup = 'Data Transactions';
    protected static ?int $navigationSort = 15;
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                Transaction::query()->with(['items.product', 'customer', 'receivablePayments']) // ← relasi dimuat di sini
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
                        'unpaid' => 'Belum Lunas',
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
                Tables\Filters\SelectFilter::make('archived')
                    ->options([
                        '0' => 'Aktif',
                        '1' => 'Diarsipkan',
                    ])
                    ->label('Status Arsip')
                    ->default('0'),

                Tables\Filters\Filter::make('created_between')
                    ->label('Tanggal Transaksi')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Dari'),
                        Forms\Components\DatePicker::make('to')->label('Sampai'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn ($q) => $q->whereDate('created_at', '>=', $data['from']))
                            ->when($data['to'], fn ($q) => $q->whereDate('created_at', '<=', $data['to']));
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('addCustomer')
                        ->label('Tambah Pelanggan')
                        ->icon('heroicon-o-user-plus')
                        ->form([
                            Forms\Components\Select::make('customer_id')
                                ->label('Pilih Pelanggan')
                                ->relationship('customer', 'name') // otomatis ambil dari relasi
                                ->searchable()
                                ->preload()
                                ->required(),
                        ])
                        ->action(function ($record, array $data): void {
                            $record->update([
                                'customer_id' => $data['customer_id'],
                            ]);
                        })
                        ->modalHeading('Tambah / Ubah Pelanggan')
                        ->modalButton('Simpan'),

                    Tables\Actions\Action::make('detail')
                        ->icon('heroicon-o-eye')
                        ->modalHeading('Detail Transaction')
                        ->modalContent(function ($record) {
                            $record->loadMissing(['customer', 'items.product', 'receivablePayments']);
                            return view('filament.resources.transaction-detail', compact('record'));
                        }),

                    Tables\Actions\Action::make('cetakStruk')
                        ->label('Cetak Struk')
                        ->icon('heroicon-o-printer')
                        ->url(fn ($record) => match ($record->status) {
                            'paid'    => route('print.struk', $record),         // transaksi lunas
                            'partial' => route('print.struk.piutang', $record), // piutang sebagian
                            'unpaid'  => route('print.struk.piutang', $record), // piutang belum bayar
                            default   => route('print.struk', $record),         // fallback
                        })
                        ->openUrlInNewTab(),
                    
                    Tables\Actions\DeleteAction::make()
                        ->label('Delete')
                        ->requiresConfirmation()
                        ->icon('heroicon-o-trash'),
                ])
                ->icon('heroicon-o-ellipsis-vertical')
                ->tooltip('Opsi Transaksi'),
            ])            
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                Tables\Actions\BulkAction::make('archive')
                    ->label('Arsipkan')
                    ->action(fn (Collection $records) => $records->each->update(['archived' => true]))
                    ->requiresConfirmation()
                    ->color('gray')
                    ->icon('heroicon-o-archive-box'),

                    
                Tables\Actions\BulkAction::make('unarchive') // fitur baru
                    ->label('Kembalikan dari Arsip')
                    ->action(fn (Collection $records) => $records->each->update(['archived' => false]))
                    ->requiresConfirmation()
                    ->color('success')
                    ->icon('heroicon-o-arrow-uturn-left'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
        ];
    }
}
