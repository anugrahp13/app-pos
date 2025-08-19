<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'customer_name',
        'user_id',
        'total',
        'paid',
        'change',
        'archived',
        'is_debt',
        'due_date',
        'initial_payment',
        'status',
    ];
    
    public function items()
    {
        return $this->hasMany(TransactionItem::class, 'transaction_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function receivablePayments()
    {
        return $this->hasMany(ReceivablePayment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Hitung sisa hutang (piutang)
    public function remainingDebt()
    {
        // total_price - total pembayaran yang sudah dilakukan
        $paid = $this->receivablePayments()->sum('amount'); // atau pakai initial_payment kalau hanya sekali
        return $this->total - $paid;
    }

    // Cek apakah transaksi sudah lunas
    public function isPaid()
    {
        return $this->remainingDebt() <= 0;
    }
}
