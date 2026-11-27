<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\EmailVerificationCodeController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProfileImageController;
use App\Services\ProfileImageService;
use App\Http\Controllers\ProjectInteractionController;

Route::get('/', function () {
    return redirect('/login');
});


Route::middleware(['auth', 'verified'])->group(function () {

    // Crear proyecto
    Route::get('/proyectos/create/{slug?}', [ProjectController::class, 'create'])
        ->name('projects.create')
        ->middleware('auth');

    // Mis proyectos
    Route::get('/proyectos/mis-proyectos', [ProjectController::class, 'myProjects'])
        ->name('projects.myProjects');

    // Mostrar proyecto
    Route::get('/proyectos/{project:slug}', [ProjectController::class, 'show'])
        ->name('projects.show');
    
    Route::delete('/proyectos/delete/{project}', [ProjectController::class, 'delete'])
        ->name('projects.delete');

});

Route::middleware(['auth', 'verified'])->group(function () {

    // Reacciones
    Route::post(
        '/proyectos/{project:slug}/reaccion',
        [ProjectInteractionController::class, 'toggleReaction']
    )->name('projects.reaction.toggle');

    // Comentarios
    Route::post(
        '/proyectos/{project:slug}/comentarios',
        [ProjectInteractionController::class, 'storeComment']
    )->name('projects.comments.store');

    Route::delete(
        '/comentarios/{comment}',
        [ProjectInteractionController::class, 'deleteComment']
    )->name('projects.comments.delete');

    // Colaboración
    Route::post(
        '/proyectos/{project:slug}/colaboracion',
        [ProjectInteractionController::class, 'storeCollaboration']
    )->name('projects.collaboration.store');

    Route::patch(
        '/colaboraciones/{collaborationRequest}',
        [ProjectInteractionController::class, 'updateCollaborationStatus']
    )->name('projects.collaboration.update');

    Route::patch(
        '/colaboraciones/{collaborationRequest}/cancelar',
        [ProjectInteractionController::class, 'cancelCollaboration']
    )->name('projects.collaboration.cancel');

    // Actualizaciones
    Route::post(
        '/proyectos/{project:slug}/actualizaciones',
        [ProjectInteractionController::class, 'storeUpdate']
    )->name('projects.updates.store');

    Route::delete(
        '/actualizaciones/{update}',
        [ProjectInteractionController::class, 'deleteUpdate']
    )->name('projects.updates.delete');

});

Route::post('/projects', [ProjectController::class, 'store'])
    ->name('projects.store');

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
