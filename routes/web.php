<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SSOController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [SSOController::class, 'redirect'])->name('login');

Route::get('/auth/callback', [SSOController::class, 'callback'])->name('sso.callback');

Route::get('/dashboard', function() {
    return view('dashboard');
})->middleware('auth');

Route::post('/logout', function() {
    Auth::logout();
    return redirect('/');
})->name('logout');
