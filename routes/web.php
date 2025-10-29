<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Inventory\PemesananController;
use App\Http\Controllers\Inventory\PenerimaanController;
use App\Http\Controllers\Inventory\BastController;
use App\Http\Controllers\Inventory\ItemController;
use App\Http\Controllers\Inventory\NotifikasiController;
use App\Http\Controllers\Auth\SSOController;

// ==================== HALAMAN UTAMA ====================
Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('after.sso')
        : view('welcome');
})->name('home');

// ==================== AUTENTIKASI SSO ====================
// Login: arahkan ke SSO provider
Route::get('/login', function () {
    if (Auth::check()) {
        return redirect()->route('after.sso');
    }
    return app(SSOController::class)->redirect();
})->name('login');

// Callback: diterima dari SSO provider
Route::get('/auth/callback', [SSOController::class, 'callback'])->name('sso.callback');

// Logout dari sistem + SSO
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

// ==================== REDIRECT SETELAH LOGIN SSO ====================
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

// ==================== DASHBOARD PER ROLE ====================
Route::middleware(['auth'])->group(function () {
    Route::view('/super-admin/dashboard', 'super-admin.dashboard')->name('super-admin.dashboard');
    Route::view('/tim-ppk/dashboard', 'tim-ppk.dashboard')->name('tim-ppk.dashboard');
    Route::view('/instalasi/dashboard', 'instalasi.dashboard')->name('instalasi.dashboard');
    Route::redirect('/dashboard', '/after-sso')->name('dashboard');
});

// ==================== INVENTORY MODULE (DENGAN MIDDLEWARE AUTH) ====================
Route::middleware(['auth'])->group(function () {

    // PEMESANAN (Bagian Instalasi / PPK)
    Route::prefix('pemesanan')->group(function () {
        Route::get('/', [PemesananController::class, 'index'])->name('pemesanan.index');
        Route::get('/create', [PemesananController::class, 'create'])->name('pemesanan.create');
        Route::post('/store', [PemesananController::class, 'store'])->name('pemesanan.store');
        Route::get('/{id}', [PemesananController::class, 'show'])->name('pemesanan.show');
        Route::get('/{id}/download', [PemesananController::class, 'downloadStruk'])->name('pemesanan.download');
    });

    // PENERIMAAN (Bagian Gudang / Teknisi)
    Route::prefix('penerimaan')->group(function () {
        Route::get('/', [PenerimaanController::class, 'index'])->name('penerimaan.index');
        Route::get('/{id}', [PenerimaanController::class, 'show'])->name('penerimaan.show');
        Route::post('/{id}/update-status', [PenerimaanController::class, 'updateStatus'])->name('penerimaan.updateStatus');
        Route::post('/{id}/confirm', [PenerimaanController::class, 'confirm'])->name('penerimaan.confirm');
    });

    // BAST (Berita Acara Serah Terima)
    Route::prefix('bast')->group(function () {
        Route::get('/{id}/download', [BastController::class, 'download'])->name('bast.download');
        Route::post('/{id}/upload', [BastController::class, 'upload'])->name('bast.upload');
    });

    // STOK BARANG
    Route::prefix('stok')->group(function () {
        Route::get('/', [ItemController::class, 'index'])->name('stok.index');
    });

    // NOTIFIKASI
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
});
