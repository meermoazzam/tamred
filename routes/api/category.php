<?php

use App\Http\Controllers\Api\CategoryController;
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
    Route::get('/categories/{id}', [CategoryController::class, 'get']);
    Route::get('/categories', [CategoryController::class, 'list']);

    Route::middleware(['admin'])->group(function () {
        // Only admin available routes
    });
});
