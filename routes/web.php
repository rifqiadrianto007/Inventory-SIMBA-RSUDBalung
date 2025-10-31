<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\SSOController;
use App\Http\Middleware\PreventBackHistory;

// ROUTE: halaman utama
Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('after.sso')
        : redirect()->route('login');
})->name('home');

// ROUTE: login otomatis ke SSO
Route::get('/login', function () {
    if (Auth::check()) {
        return redirect()->route('after.sso');
    }
    return app(SSOController::class)->redirect();
})->name('login');

// ROUTE: callback setelah login dari SSO
Route::get('/auth/callback', [SSOController::class, 'callback'])->name('sso.callback');

// ROUTE: setelah login (pengarahan berdasarkan role)
Route::middleware(['auth', PreventBackHistory::class])->get('/after-sso', function () {
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

// ROUTE: dashboard masing-masing role
Route::middleware(['auth', PreventBackHistory::class])->group(function () {
    Route::get('/super-admin/dashboard', fn() => view('super-admin.dashboard'))
        ->name('super-admin.dashboard');

    Route::get('/tim-ppk/dashboard', fn() => view('tim-ppk.dashboard'))
        ->name('tim-ppk.dashboard');

    Route::get('/instalasi/dashboard', fn() => view('instalasi.dashboard'))
        ->name('instalasi.dashboard');

    Route::get('/admin-gudang-umum/dashboard', fn() => view('admin-gudang-umum.dashboard'))
        ->name('admin-gudang-umum.dashboard');

    Route::get('/penanggung-jawab/dashboard', fn() => view('penanggung-jawab.dashboard'))
        ->name('penanggung-jawab.dashboard');

    Route::get('/tim-teknis/dashboard', fn() => view('tim-teknis.dashboard'))
        ->name('tim-teknis.dashboard');

    Route::get('/dashboard', fn() => redirect()->route('after.sso'))
        ->name('dashboard');
});

// ROUTE: logout (redirect ke SSO logout)
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    $returnTo  = route('home');
    $ssoLogout = config('services.laravelpassport.logout_url');

    return $ssoLogout
        ? redirect($ssoLogout . '?redirect=' . urlencode($returnTo))
        : redirect($returnTo);
})->name('logout');
