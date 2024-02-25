<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Web\StaticController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware(['guest'])->group(function () {
    Route::get('/login', function () { return view('admin.auth.login'); })->name('login.get');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});


Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AuthController::class, 'getUsers'])->name('users.get');
    Route::get('/posts', [AuthController::class, 'getPosts'])->name('posts.get');
    Route::get('/albums', [AuthController::class, 'getAlbums'])->name('albums.get');
    Route::get('/categories', [AuthController::class, 'getCategories'])->name('categories.get');







    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});
