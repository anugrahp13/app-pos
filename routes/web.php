<?php

use App\Http\Controllers\TransactionPrintController;
use Illuminate\Support\Facades\Route;
use App\Livewire\OrderTransaction;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/transaction', OrderTransaction::class)->name('transaction.livewire');

Route::get('/print-struk/{transaction}', function (Transaction $transaction) {
        return view('invoice.transaction', ['transaction' => $transaction]);
    })->name('print.struk');

// route baru untuk transaksi piutang
Route::get('/print-struk-piutang/{transaction}', function (Transaction $transaction) {
    return view('invoice.transaction-debt', ['transaction' => $transaction]);
})->name('print.struk.piutang');