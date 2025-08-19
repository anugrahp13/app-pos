<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceivablePayment extends Model
{
    protected $fillable = [
        'transaction_id',
        'amount',
        'paid_at',
    ];
}
