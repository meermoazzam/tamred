<?php

use App\Http\Controllers\Api\AuthController;
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

Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/delete', [AuthController::class, 'deleteTest']);

Route::post('/password/forgot', [AuthController::class, 'forgotPassword']);
Route::post('/password/reset', [AuthController::class, 'resetPassword']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::middleware(['verified'])->group(function () {
        Route::post('/password/update', [AuthController::class, 'updatePassword']);
    });
    // Request OTP for phone verification
    Route::post('/email/verification/request', [AuthController::class, 'requestEmailVerification']);
    Route::post('/email/verification', [AuthController::class, 'emailVerification']);
});
