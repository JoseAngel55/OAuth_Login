<?php

use App\Http\Controllers\Auth\OAuthController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// ─── Rutas públicas ───────────────────────────────────────────────────────────

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('auth.login');
})->middleware('guest')->name('login');

// Logout
Route::post('/logout', function () {
    \Illuminate\Support\Facades\Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->middleware('auth')->name('logout');

// ─── OAuth 2.0 Routes ─────────────────────────────────────────────────────────
// Soporta: discord, twitch
Route::prefix('auth/{provider}')->group(function () {
    // Paso 1: Redirigir al proveedor
    Route::get('/redirect', [OAuthController::class, 'redirect'])
        ->middleware('guest')
        ->name('oauth.redirect');

    // Paso 2: Callback del proveedor
    Route::get('/callback', [OAuthController::class, 'callback'])
        ->middleware('guest')
        ->name('oauth.callback');
});

// Desconectar proveedor (requiere auth)
Route::delete('/auth/{provider}/disconnect', [OAuthController::class, 'disconnect'])
    ->middleware('auth')
    ->name('oauth.disconnect');

// ─── Dashboard (protegida) ────────────────────────────────────────────────────

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});