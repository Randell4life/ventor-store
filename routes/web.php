<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerAccountController;
use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;

Route::get('/', [StoreController::class, 'home'])->name('home');
Route::get('/catalog', [StoreController::class, 'catalog'])->name('catalog');
Route::get('/product/{product:slug}', [StoreController::class, 'show'])->name('product.show');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{product}', [CartController::class, 'remove'])->name('cart.remove');

Route::get('/checkout', [CheckoutController::class, 'create'])->name('checkout');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::post('/checkout/coupon', [CheckoutController::class, 'applyCoupon'])->name('checkout.coupon.apply');
Route::post('/checkout/coupon/remove', [CheckoutController::class, 'removeCoupon'])->name('checkout.coupon.remove');
Route::get('/order/{token}/success', [CheckoutController::class, 'success'])
    ->name('checkout.success');

Route::middleware('guest')->group(function () {
    Route::get('/register', [CustomerAccountController::class, 'showRegister'])->name('register');
    Route::post('/register', [CustomerAccountController::class, 'register'])->name('register.store');
    Route::get('/login', [CustomerAccountController::class, 'showLogin'])->name('login');
    Route::post('/login', [CustomerAccountController::class, 'login'])->name('login.store');
});

Route::middleware('auth')->prefix('account')->name('account.')->group(function () {
    Route::get('/', [CustomerAccountController::class, 'index'])->name('index');
    Route::get('/profile', [CustomerAccountController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile', [CustomerAccountController::class, 'updateProfile'])->name('profile.update');
    Route::get('/orders', [CustomerAccountController::class, 'orders'])->name('orders');
    Route::get('/rewards', [CustomerAccountController::class, 'rewards'])->name('rewards');
    Route::get('/orders/{order}', [CustomerAccountController::class, 'showOrder'])->name('orders.show');
    Route::post('/logout', [CustomerAccountController::class, 'logout'])->name('logout');
});
