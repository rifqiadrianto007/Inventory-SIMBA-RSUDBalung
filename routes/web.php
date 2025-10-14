<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SSOController;

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

Route::get('/login', [SSOController::class, 'redirect'])->name('login');

Route::get('/auth/callback', [SSOController::class, 'callback'])->name('sso.callback');

Route::get('/dashboard', function() {
    return view('dashboard');
})->middleware('auth');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    $returnTo = route('login');
    $ssoLogout = config('services.laravelpassport.logout_url');

    return $ssoLogout
        ? redirect($ssoLogout.'?redirect='.urlencode($returnTo))
        : redirect($returnTo);
})->name('logout');
