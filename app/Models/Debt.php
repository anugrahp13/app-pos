<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Debt extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'customer_name',
        'due_date',
        'initial_payment',
        'total',
        'remaining',
        'status',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
