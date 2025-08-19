<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaidReceivableResource\Pages;
use App\Models\ReceivablePayment;
use App\Models\Transaction;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class PaidReceivableResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-circle';
    protected static ?string $navigationLabel = 'Piutang Lunas';
    protected static ?string $pluralModelLabel = 'Data Piutang Lunas';
    protected static ?string $modelLabel = 'Piutang Lunas';
    protected static ?string $navigationGroup = 'Data Transactions';
    protected static ?int $navigationSort = 20;

    public static function getEloquentQuery(): Builder
    {
        return Transaction::query()
            ->selectRaw('MAX(id) as id, customer_id')
            ->where('is_debt', true)
            ->where('status', 'paid')
            ->groupBy('customer_id');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer.name')
                    ->label('Pelanggan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('jumlah_piutang')
                    ->label('Jumlah Piutang')
                    ->state(function (Transaction $record) {
                        return $record->customer->transactions()
                            ->where('is_debt', true)
                            ->where('status', 'paid')
                            ->count() . ' piutang lunas';
                    }),

                TextColumn::make('total_dibayar')
                    ->label('Total Dibayar')
                    ->state(function (Transaction $record) {
                        return 'Rp ' . number_format(
                            $record->customer->transactions()
                                ->where('is_debt', true)
                                ->where('status', 'paid')
                                ->sum('initial_payment'),
                            0, ',', '.'
                        );
                    }),
                
                TextColumn::make('tanggal_hutang')
                    ->label('Tanggal Hutang')
                    ->state(function (Transaction $record) {
                        return optional(
                            $record->customer->transactions()
                                ->where('is_debt', true)
                                ->where('status', 'paid')
                                ->oldest('created_at')
                                ->first()
                        )?->created_at->format('d M Y H:i');
                    }),

                TextColumn::make('tanggal_bayar_terakhir')
                    ->label('Sudah Dibayar')
                    ->state(function (Transaction $record) {
                        $lastPayment = ReceivablePayment::whereIn(
                            'transaction_id',
                            $record->customer->transactions()->pluck('id')
                        )->latest('paid_at')->first();

                        return $lastPayment
                            ? \Carbon\Carbon::parse($lastPayment->paid_at)->format('d M Y H:i')
                            : '-';
                    }),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors(['success'])
                    ->state('Lunas'),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaidReceivables::route('/'),
        ];
    }
}
