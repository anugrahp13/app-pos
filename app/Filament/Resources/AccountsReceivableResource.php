<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AccountsReceivableResource\Pages;
use App\Models\Customer;
use App\Models\ReceivablePayment;
use App\Models\Transaction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class AccountsReceivableResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'Piutang';
    protected static ?string $pluralModelLabel = 'Data Piutang';
    protected static ?string $modelLabel = 'Piutang';
    protected static ?string $navigationGroup = 'Data Transactions';
    protected static ?int $navigationSort = 20;

    public static function getEloquentQuery(): Builder
    {
        return Customer::query()
            ->whereHas('transactions', function ($query) {
                $query->where('is_debt', true)
                    ->whereIn('status', ['unpaid', 'partial']);
            });
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Pelanggan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('jumlah_piutang')
                    ->label('Jumlah Piutang')
                    ->state(function (Customer $record) {
                        return $record->transactions()
                            ->where('is_debt', true)
                            ->whereIn('status', ['unpaid', 'partial'])
                            ->count() . ' piutang aktif';
                    }),

                TextColumn::make('total_belum_dibayar')
                    ->label('Total Belum Dibayar')
                    ->state(function (Customer $record) {
                        $totalBelumDibayar = 0;
                
                        foreach ($record->transactions()
                            ->where('is_debt', true)
                            ->whereIn('status', ['unpaid', 'partial'])
                            ->with('receivablePayments')
                            ->get() as $trx) {
                
                            $dibayar = ($trx->initial_payment ?? 0) + ($trx->receivablePayments->sum('amount') ?? 0);
                            $sisa = max($trx->total - $dibayar, 0);
                
                            $totalBelumDibayar += $sisa;
                        }
                
                        return 'Rp ' . number_format($totalBelumDibayar, 0, ',', '.');
                    }),
                

                TextColumn::make('tanggal_hutang')
                    ->label('Tanggal Hutang')
                    ->state(function (Customer $record) {
                        $firstDebt = $record->transactions()
                            ->where('is_debt', true)
                            ->whereIn('status', ['unpaid', 'partial'])
                            ->oldest('created_at')
                            ->first();

                        return optional($firstDebt)?->created_at->format('d M Y H:i') ?? '-';
                    }),

                BadgeColumn::make('status_piutang')
                    ->label('Status')
                    ->state(function (Customer $record) {
                        $total = $record->transactions()
                            ->where('is_debt', true)
                            ->whereIn('status', ['unpaid', 'partial'])
                            ->sum('total');

                        $paid = $record->transactions()
                            ->where('is_debt', true)
                            ->whereIn('status', ['unpaid', 'partial'])
                            ->sum('initial_payment');

                        $remaining = $total - $paid;

                        return match (true) {
                            $remaining <= 0 => 'paid',
                            $paid == 0 => 'unpaid',
                            default => 'partial',
                        };
                    })
                    ->colors([
                        'danger' => 'unpaid',
                        'warning' => 'partial',
                        'success' => 'paid',
                    ])
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'unpaid' => 'Belum Bayar',
                        'partial' => 'Sebagian',
                        'paid' => 'Lunas',
                        default => ucfirst($state),
                    }),
            ])
            ->actions([
                Action::make('detail')
                    ->label('Detail')
                    ->icon('heroicon-o-eye')
                    ->modalHeading(fn (Customer $record) => 'Detail Semua Piutang - ' . $record->name)
                    ->modalContent(function (Customer $record) {
                        $transactions = $record->transactions()
                            ->where('is_debt', true)
                            ->whereIn('status', ['unpaid', 'partial'])
                            ->with(['items.product', 'user', 'receivablePayments'])
                            ->latest()
                            ->get();

                        return new HtmlString(view('filament.resources.receivables-detail', [
                            'customer' => $record,
                            'transactions' => $transactions,
                        ])->render());
                    }),
                Action::make('bayar')
                    ->label('Bayar')
                    ->color('success')
                    ->form(function (Customer $record) {
                        $debts = $record->transactions()
                            ->where('is_debt', true)
                            ->whereIn('status', ['unpaid', 'partial'])
                            ->get();

                        return [
                            Select::make('transaction_id')
                                ->label('Pilih Piutang')
                                ->options(
                                    $debts->mapWithKeys(function ($debt) {
                                        $dibayar = ($debt->initial_payment ?? 0) + $debt->receivablePayments()->sum('amount');
                                        $sisa = max($debt->total - $dibayar, 0);

                                        return [
                                            $debt->id => 'ID #' . $debt->id . ' | Sisa: Rp ' . number_format($sisa, 0, ',', '.')
                                        ];
                                    })
                                )
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function (Set $set, $state) use ($debts) {
                                    $selectedDebt = $debts->firstWhere('id', $state);

                                    if ($selectedDebt) {
                                        $dibayar = ($selectedDebt->initial_payment ?? 0) + $selectedDebt->receivablePayments()->sum('amount');
                                        $sisa = max($selectedDebt->total - $dibayar, 0);
                                        $set('max_payment', $sisa);
                                    } else {
                                        $set('max_payment', null);
                                    }
                                }),

                            TextInput::make('payment_amount')
                                ->label('Jumlah Pembayaran')
                                ->numeric()
                                ->required()
                                ->minValue(500)
                                ->maxValue(fn (callable $get) => $get('max_payment')),

                            TextInput::make('max_payment')->hidden(),
                        ];
                    })
                    ->action(function (Customer $record, array $data) {
                        $transaction = Transaction::find($data['transaction_id']);
                        $jumlah = $data['payment_amount'];

                        // $transaction->initial_payment += $jumlah;

                         // Hitung total sudah dibayar = dp + cicilan
                        $dibayar = ($transaction->initial_payment ?? 0) + $transaction->receivablePayments()->sum('amount') + $jumlah;

                        $transaction->status = $dibayar >= $transaction->total ? 'paid' : 'partial';
                        $transaction->save();

                        ReceivablePayment::create([
                            'transaction_id' => $transaction->id,
                            'amount' => $jumlah,
                            'paid_at' => now(),
                        ]);
                    })
            ])
            ->defaultSort('name');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAccountsReceivables::route('/'),
        ];
    }
}
