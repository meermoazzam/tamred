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

Route::get('/users/delete/action', [UserController::class, 'deleteAction'])->name('user.account.delete');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/users/me', [UserController::class, 'whoAmI']);
    Route::get('/users/on/load', [UserController::class, 'onLoadData']);
});

Route::middleware(['auth:sanctum', 'active'])->group(function () {

    Route::post('/users/delete/request', [UserController::class, 'deleteRequest']);

    Route::put('/users', [UserController::class, 'update']);
    Route::post('/users/profile-picture/upload', [UserController::class, 'updateProfilePicture']);
    Route::post('/users/device-id/update', [UserController::class, 'updateDeviceId']);

    Route::post('/users/categories', [UserController::class, 'attachCategories']);
    Route::get('/users/{id}', [UserController::class, 'get']);
    Route::get('/users/by/username/{username}', [UserController::class, 'getByUsername']);
    Route::get('/users', [UserController::class, 'list']);
    Route::get('/users/by/suggestion', [UserController::class, 'listBySuggestion']);
    Route::post('/users/follow', [UserController::class, 'follow']);
    Route::post('/users/unfollow', [UserController::class, 'unfollow']);
    Route::post('/users/block', [UserController::class, 'block']);
    Route::post('/users/unblock', [UserController::class, 'unblock']);

    Route::get('/users/{id}/followers', [UserController::class, 'followerList']);
    Route::get('/users/{id}/following', [UserController::class, 'followingList']);
    Route::get('/users/my/friends', [UserController::class, 'friendsList']);
});
