<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'barcode',
        'category_id',
        'purchase_price',
        'sell_price',
        'stock',
        'unit',
        'image',
        'description',
        'status',
    ];

    /**
     * Relasi ke kategori produk
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
