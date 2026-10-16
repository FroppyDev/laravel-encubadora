<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmailVerificationCodeController;
use App\Http\Controllers\HomeController;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/home', [HomeController::class, 'index'])->middleware(['auth', 'verified'])->name('home');
Route::patch('/profile', [HomeController::class, 'updateProfile'])->middleware(['auth', 'verified'])->name('profile.update');

Route::middleware(['auth', 'throttle:6,1'])->group(function () {
    Route::post('/email/verification-code', [EmailVerificationCodeController::class, 'verify'])->name('verification.code');
    Route::post('/email/verification-code/resend', [EmailVerificationCodeController::class, 'resend'])->name('verification.code.resend');
});
