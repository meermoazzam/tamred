<?php

use Illuminate\Http\Request;
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

include __DIR__ . '/api/auth.php';
include __DIR__ . '/api/album.php';
include __DIR__ . '/api/itineraries.php';
include __DIR__ . '/api/category.php';
include __DIR__ . '/api/chat.php';
include __DIR__ . '/api/comment.php';
include __DIR__ . '/api/post.php';
include __DIR__ . '/api/media.php';
include __DIR__ . '/api/user.php';

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
