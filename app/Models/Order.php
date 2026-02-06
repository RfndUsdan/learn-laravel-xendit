<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'product_id',
        'checkout_link',
        'external_id',
        'status'
    ];

    // Relasi: Satu order milik satu produk
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}