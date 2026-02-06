<?php

use App\Http\Controllers\ProfileController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

Route::get('/dashboard', function () {
    // Mengambil semua data produk dari database
    $products = Product::all(); 
    
    // Mengirimkan variabel $products ke file dashboard.blade.php
    return view('dashboard', compact('products'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/checkout', [PaymentController::class, 'checkout'])->name('checkout');
});

require __DIR__.'/auth.php';
