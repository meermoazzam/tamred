<?php

use App\Http\Controllers\Api\ItinController;
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
    Route::post('/itin', [ItinController::class, 'create']);
    Route::get('/itin', [ItinController::class, 'list']);
    Route::get('/itin/{id}', [ItinController::class, 'get']);
    Route::put('/itin/{id}', [ItinController::class, 'update']);
    Route::delete('/itin/{id}', [ItinController::class, 'delete']);
});
