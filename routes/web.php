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

// OAuth login flow for MCP clients (Passport needs a web session)
// Uses a separate path so /login stays as the SPA catch-all (no conflict)
Route::get('/login', [GoogleAuthController::class, 'oauthLogin'])->name('login');
Route::get('/auth/google/oauth-callback', [GoogleAuthController::class, 'oauthCallback'])->name('google.oauth-callback');

// SPA catch-all — exclude api/, oauth/, and .well-known/ paths
Route::get('{any}', function () {
    return view('app');
})->where('any', '^(?!api/|oauth/|\.well-known/).*$')->name('spa');
