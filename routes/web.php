<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\SSOController;

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('after.sso')
        : redirect()->route('login');
})->name('home');

Route::get('/login', function () {
    if (Auth::check()) {
        return redirect()->route('after.sso');
    }
    return app(SSOController::class)->redirect();
})->name('login');

Route::get('/auth/callback', [SSOController::class, 'callback'])->name('sso.callback');

Route::middleware(['auth'])->get('/after-sso', function () {
    info('[AFTER-SSO]', ['check' => Auth::check(), 'user' => Auth::id()]);

    $role = strtolower(Auth::user()->role ?? '');

    if ($role === '') {
        Auth::logout();
        return redirect('/')->with('error', 'Akun belum memiliki role.');
    }

    return match ($role) {
        'super-admin' => redirect()->route('super-admin.dashboard'),
        'tim-ppk'     => redirect()->route('tim-ppk.dashboard'),
        'instalasi'   => redirect()->route('instalasi.dashboard'),
        default       => abort(403, 'Role tidak dikenali.'),
    };
})->name('after.sso');

Route::middleware(['auth'])->get('/super-admin/dashboard', fn () => view('super-admin.dashboard'))
    ->name('super-admin.dashboard');

Route::middleware(['auth'])->get('/tim-ppk/dashboard', fn () => view('tim-ppk.dashboard'))
    ->name('tim-ppk.dashboard');

Route::middleware(['auth'])->get('/instalasi/dashboard', fn () => view('instalasi.dashboard'))
    ->name('instalasi.dashboard');

Route::middleware(['auth'])->get('/dashboard', fn () => redirect()->route('after.sso'))
    ->name('dashboard');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    $returnTo  = route('home');
    $ssoLogout = config('services.laravelpassport.logout_url'); 

    return $ssoLogout
        ? redirect($ssoLogout.'?redirect='.urlencode($returnTo))
        : redirect($returnTo);
})->name('logout');

