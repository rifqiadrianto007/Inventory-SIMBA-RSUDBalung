<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Inventory Controllers
use App\Http\Controllers\Inventory\UserController;
use App\Http\Controllers\Inventory\FAQController;
use App\Http\Controllers\Inventory\PemesananController;
use App\Http\Controllers\Inventory\PenerimaanController;
use App\Http\Controllers\Inventory\BastController;
use App\Http\Controllers\Inventory\ItemController;
use App\Http\Controllers\Inventory\NotifikasiController;
use App\Http\Controllers\Inventory\JabatanController;

/*
|--------------------------------------------------------------------------
| PUBLIC PAGE
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('after.login')
        : view('welcome');
})->name('home');

/*
|--------------------------------------------------------------------------
| AUTH (LOGIN MANUAL, SSO NON-AKTIF)
|--------------------------------------------------------------------------
*/
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->route('after.login');
    }

    return back()->withErrors([
        'email' => 'Email atau password salah.',
    ]);
})->name('login.submit');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('home');
})->name('logout');

/*
|--------------------------------------------------------------------------
| REDIRECT AFTER LOGIN (ROLE BASED)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->get('/after-login', function () {
    $role = strtolower(Auth::user()->role ?? '');

    return match ($role) {
        'super admin'        => redirect()->route('dashboard.super-admin'),
        'admin gudang umum'  => redirect()->route('dashboard.admin-gudang'),
        'penanggung jawab'   => redirect()->route('dashboard.penanggung-jawab'),
        'ppk'                => redirect()->route('dashboard.ppk'),
        'teknis'             => redirect()->route('dashboard.teknis'),
        'instalasi'          => redirect()->route('dashboard.instalasi'),
        default              => abort(403, 'Role tidak dikenali'),
    };
})->name('after.login');

/*
|--------------------------------------------------------------------------
| DASHBOARD PER ROLE
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::view('/dashboard/super-admin', 'dashboard.super-admin')->name('dashboard.super-admin');
    Route::view('/dashboard/admin-gudang', 'dashboard.admin-gudang')->name('dashboard.admin-gudang');
    Route::view('/dashboard/penanggung-jawab', 'dashboard.penanggung-jawab')->name('dashboard.penanggung-jawab');
    Route::view('/dashboard/ppk', 'dashboard.ppk')->name('dashboard.ppk');
    Route::view('/dashboard/teknis', 'dashboard.teknis')->name('dashboard.teknis');
    Route::view('/dashboard/instalasi', 'dashboard.instalasi')->name('dashboard.instalasi');

    Route::redirect('/dashboard', '/after-login')->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| USER MANAGEMENT
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('akun')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('akun.index');
    Route::get('/profile/me', [UserController::class, 'profile'])->name('akun.profile');
    Route::get('/{id}', [UserController::class, 'show'])->name('akun.show');
    Route::get('/{id}/edit', [UserController::class, 'edit'])->name('akun.edit');
    Route::put('/{id}', [UserController::class, 'update'])->name('akun.update');
});

/*
|--------------------------------------------------------------------------
| JABATAN MANAGEMENT
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('jabatan')->group(function () {
    Route::get('/', [JabatanController::class, 'index'])->name('jabatan.index');
    Route::get('/create', [JabatanController::class, 'create'])->name('jabatan.create');
    Route::post('/store', [JabatanController::class, 'store'])->name('jabatan.store');
    Route::get('/{id}/edit', [JabatanController::class, 'edit'])->name('jabatan.edit');
    Route::put('/{id}', [JabatanController::class, 'update'])->name('jabatan.update');
    Route::delete('/{id}', [JabatanController::class, 'destroy'])->name('jabatan.destroy');
});

/*
|--------------------------------------------------------------------------
| FAQ
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->get('/faq', [FAQController::class, 'index'])->name('faq.index');

/*
|--------------------------------------------------------------------------
| INVENTORY MODULE
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // Pemesanan
    Route::prefix('pemesanan')->group(function () {
        Route::get('/', [PemesananController::class, 'index'])->name('pemesanan.index');
        Route::get('/create', [PemesananController::class, 'create'])->name('pemesanan.create');
        Route::post('/store', [PemesananController::class, 'store'])->name('pemesanan.store');
        Route::get('/{id}', [PemesananController::class, 'show'])->name('pemesanan.show');
        Route::get('/{id}/download', [PemesananController::class, 'downloadStruk'])->name('pemesanan.download');
    });

    // Penerimaan
    Route::prefix('penerimaan')->group(function () {
        Route::get('/', [PenerimaanController::class, 'index'])->name('penerimaan.index');
        Route::get('/{id}', [PenerimaanController::class, 'show'])->name('penerimaan.show');
        Route::post('/{id}/update-status', [PenerimaanController::class, 'updateStatus'])->name('penerimaan.updateStatus');
        Route::post('/{id}/confirm', [PenerimaanController::class, 'confirm'])->name('penerimaan.confirm');
    });

    // BAST
    Route::prefix('bast')->group(function () {
        Route::get('/{id}/download', [BastController::class, 'download'])->name('bast.download');
        Route::post('/{id}/upload', [BastController::class, 'upload'])->name('bast.upload');
    });

    // Stok
    Route::get('/stok', [ItemController::class, 'index'])->name('stok.index');

    // Notifikasi
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
});
