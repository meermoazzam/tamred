<?php

use App\Http\Controllers\Api\PostController;
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
    Route::post('/posts', [PostController::class, 'create']);
    Route::post('/posts/{id}', [PostController::class, 'publish']);
    Route::get('/posts/{id}', [PostController::class, 'get']);
    Route::get('/posts', [PostController::class, 'list']);
    Route::put('/posts/{id}', [PostController::class, 'update']);
    Route::delete('/posts/{id}', [PostController::class, 'delete']);

    Route::put('/posts/{id}/category', [PostController::class, 'attachCategories']);
    Route::post('/posts/{id}/react', [PostController::class, 'react']);
    Route::get('/posts/{id}/react', [PostController::class, 'reactList']);

    Route::get('/posts/{id}/react', [PostController::class, 'reactList']);

    Route::post('/posts/media/upload', [PostController::class, 'uploadMedia']);
    Route::delete('/posts/media/delete', [PostController::class, 'deleteMedia']);
});
