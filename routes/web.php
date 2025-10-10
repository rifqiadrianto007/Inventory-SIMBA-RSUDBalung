<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SsoController;

Route::get('/', fn() => redirect()->route('login'));
Route::get('/login', fn() => redirect()->route('sso.login'))->name('login');

Route::get('/login/sso',       [SsoController::class, 'redirect'])->name('sso.login');
Route::get('/auth/callback',   [SsoController::class, 'callback'])->name('sso.callback');

Route::middleware('auth')->get('/dashboard', fn () => view('welcome'));
Route::post('/logout',         [SsoController::class, 'logout'])->name('logout');

Route::get('/_netcheck', function () {
    return \Illuminate\Support\Facades\Http::timeout(5)->get('http://sso-server.test/ping')->body();
});
