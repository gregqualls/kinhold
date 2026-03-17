<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\GoogleAuthController;

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

// Google OAuth routes (full-page redirects, not API calls)
Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');

Route::get('{any}', function () {
    return view('app');
})->where('any', '^(?!api/).*$')->name('spa');
