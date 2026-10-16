<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CollaborationController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ReactionController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\UpdateController;
use App\Models\Category;
use App\Models\Technology;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['api.identity', 'throttle:api'])->group(function () {
    Route::post('register', [AuthController::class, 'register'])->middleware('throttle:registration');
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:login');
    Route::get('email/verify/{id}/{hash}', function (string $id, string $hash) {
        $user = User::findOrFail($id);
        abort_unless(hash_equals(sha1($user->getEmailForVerification()), $hash), 403);
        if (! $user->hasVerifiedEmail() && $user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return response()->json(['message' => 'Correo verificado. Ya puedes volver a la aplicación.']);
    })->middleware(['signed', 'throttle:6,1'])->name('api.verification.verify');

    Route::get('categories', fn () => Category::where('is_active', true)->orderBy('name')->paginate(100));
    Route::get('technologies', fn () => Technology::orderBy('name')->paginate(100));
    Route::get('projects', [ProjectController::class, 'index']);
    Route::get('projects/{project}', [ProjectController::class, 'show']);
    Route::get('projects/{project}/comments', [CommentController::class, 'index']);
    Route::get('projects/{project}/updates', [UpdateController::class, 'index']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('user', [AuthController::class, 'user']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('email/verification-notification', [AuthController::class, 'resend'])->middleware('throttle:6,1');
        Route::get('me/projects', [ProjectController::class, 'mine']);
        Route::middleware('verified')->group(function () {
            Route::patch('user', [AuthController::class, 'profile']);
            Route::post('projects', [ProjectController::class, 'store']);
            Route::match(['put', 'patch'], 'projects/{project}', [ProjectController::class, 'update']);
            Route::delete('projects/{project}', [ProjectController::class, 'archive']);
            Route::post('projects/{project}/comments', [CommentController::class, 'store']);
            Route::patch('comments/{comment}', [CommentController::class, 'update']);
            Route::delete('comments/{comment}', [CommentController::class, 'destroy']);
            Route::put('projects/{project}/reaction', [ReactionController::class, 'store']);
            Route::delete('projects/{project}/reaction', [ReactionController::class, 'destroy']);
            Route::get('projects/{project}/collaboration-requests', [CollaborationController::class, 'index']);
            Route::post('projects/{project}/collaboration-requests', [CollaborationController::class, 'store']);
            Route::patch('collaboration-requests/{collaborationRequest}', [CollaborationController::class, 'update']);
            Route::post('projects/{project}/updates', [UpdateController::class, 'store']);
            Route::post('reports', [ReportController::class, 'store']);
            Route::get('reports', [ReportController::class, 'index']);
            Route::patch('reports/{report}', [ReportController::class, 'update']);
        });
    });
});
