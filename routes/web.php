<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PayPalController;


Route::get('/create-order', [PayPalController::class,'createOrder']);
Route::get('/payment/success', [PayPalController::class,'paymentSuccess'])->name('payment.success');
Route::get('/payment/cancel', [PayPalController::class,'paymentCancel'])->name('payment.cancel');
Route::get('/capture-order', [PayPalController::class,'captureOrder'])->name("capture-order");