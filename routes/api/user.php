<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/users/me', [UserController::class, 'whoAmI']);
    Route::post('/users/categories', [UserController::class, 'attachCategories']);
    Route::get('/users/{id}', [UserController::class, 'get']);
    Route::get('/users', [UserController::class, 'list']);
    Route::post('/users/follow', [UserController::class, 'follow']);
    Route::post('/users/unfollow', [UserController::class, 'unfollow']);
    Route::post('/users/block', [UserController::class, 'block']);
    Route::post('/users/unblock', [UserController::class, 'unblock']);

    Route::get('/users/{id}/followers', [UserController::class, 'followerList']);
    Route::get('/users/{id}/following', [UserController::class, 'followingList']);
});
