<?php

use App\Http\Controllers\Api\ActivityController;
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

Route::middleware(['auth:sanctum', 'active'])->group(function () {
    Route::get('/activities', [ActivityController::class, 'list']);
    Route::post('/activities/mark-read', [ActivityController::class, 'markAsRead']);
});
