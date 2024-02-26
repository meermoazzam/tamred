<?php

use App\Http\Controllers\Admin\AppController;
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
    Route::post('/login', [AppController::class, 'login'])->name('login.post');
});


Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [AppController::class, 'dashboard'])->name('dashboard');
    Route::get('/logout', [AppController::class, 'logout'])->name('logout');
});

Route::group(['prefix' => '/admin','as' => 'admin.', 'middleware' => ['auth', 'admin']], function () {
    Route::get('/users', [AppController::class, 'getUsers'])->name('users.get');
    Route::get('/posts', [AppController::class, 'getPosts'])->name('posts.get');

    Route::get('/categories/get', [AppController::class, 'getCategories'])->name('categories.get');
    Route::post('/categories/create', [AppController::class, 'createCategories'])->name('categories.create');
    Route::post('/categories/update', [AppController::class, 'updateCategories'])->name('categories.update');
    Route::post('/categories/delete', [AppController::class, 'deleteCategories'])->name('categories.delete');

    Route::get('/albums', [AppController::class, 'getAlbums'])->name('albums.get');
    Route::post('/albums/update', [AppController::class, 'updateAlbums'])->name('albums.update');
    Route::post('/albums/delete', [AppController::class, 'deleteAlbums'])->name('albums.delete');





});
