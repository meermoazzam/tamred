<?php

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

include __DIR__ . '/admin.php';

Route::get('/', function () {
    return redirect()->route('dashboard');
});


Route::get('/terms-conditions', [StaticController::class, 'termsConditions']);
Route::get('/privacy-policy', [StaticController::class, 'privacyPolicy']);
Route::get('/marketing', [StaticController::class, 'marketing']);
