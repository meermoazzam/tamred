<?php

use App\Http\Controllers\Api\ChatController;
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
    Route::post('/chat/conversations', [ChatController::class, 'createConversation']);
    Route::get('/chat/conversations', [ChatController::class, 'getConversations']);

    Route::post('/chat/messages', [ChatController::class, 'sendMessage']);
    Route::get('/chat/messages', [ChatController::class, 'getMessages']);
    Route::delete('/chat/messages/{id}', [ChatController::class, 'deleteMessage']);

    Route::get('/chat/participants', [ChatController::class, 'getParticipants']);
    Route::post('/chat/mark-read', [ChatController::class, 'markAsRead']);
});
