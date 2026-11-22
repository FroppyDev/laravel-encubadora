<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\EmailVerificationCodeController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProfileImageController;
use App\Services\ProfileImageService;

Route::get('/', function () {
    return redirect('/login');
});


Route::get('/proyectos/create', [ProjectController::class, 'create'])
    ->middleware(['auth', 'verified'])
    ->name('projects.create');


Route::get('/proyectos/{project:slug}', [ProjectController::class, 'show'])
    ->middleware(['auth', 'verified'])
    ->name('projects.show');

Route::get('/home', [HomeController::class, 'index'])->middleware(['auth', 'verified'])->name('home');

Route::patch('/profile', [HomeController::class, 'updateProfile'])->middleware(['auth', 'verified'])->name('profile.update');
Route::get('/perfil', [HomeController::class, 'profile'])
    ->middleware(['auth', 'verified'])
    ->name('profile.profile');

Route::middleware(['auth', 'throttle:6,1'])->group(function () {
    Route::post('/email/verification-code', [EmailVerificationCodeController::class, 'verify'])->name('verification.code');
    Route::post('/email/verification-code/resend', [EmailVerificationCodeController::class, 'resend'])->name('verification.code.resend');
});

Route::post('/profile/images', [ProfileImageController::class, 'update'])
    ->middleware(['auth', 'verified'])
    ->name('profile.images.update');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('dashboard');
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::patch('/categories/{category}/status', [CategoryController::class, 'status'])->name('categories.status');
});
