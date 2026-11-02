<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;

Route::get('/', [StoreController::class, 'home'])->name('home');
Route::get('/catalog', [StoreController::class, 'catalog'])->name('catalog');
Route::get('/product/{product:slug}', [StoreController::class, 'show'])->name('product.show');
Route::get('/checkout', [CheckoutController::class, 'create'])->name('checkout');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/order/{order}/success', [CheckoutController::class, 'success'])->name('checkout.success');
