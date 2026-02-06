<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    // Kolom yang boleh diisi manual/massal
    protected $fillable = [
        'name',
        'description',
        'image',
        'link',
        'price'
    ];

    // Relasi: Satu produk bisa punya banyak order
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}