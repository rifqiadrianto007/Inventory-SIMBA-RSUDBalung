<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Auth\SSOController;
use App\Http\Controllers\Inventory\JabatanController;
use App\Http\Controllers\Inventory\UserController;
use App\Http\Controllers\Inventory\FAQController;
use App\Http\Controllers\Inventory\PemesananController;
use App\Http\Controllers\Inventory\PenerimaanController;
use App\Http\Controllers\Inventory\BastController;
use App\Http\Controllers\Inventory\ItemController;
use App\Http\Controllers\Inventory\NotifikasiController;

/*
|--------------------------------------------------------------------------
| PUBLIC PAGE
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('after.sso')
        : view('welcome'); // ✅ pastikan welcome.blade.php punya tombol login SSO
})->name('home');

/*
|--------------------------------------------------------------------------
| SSO AUTH ROUTES
|--------------------------------------------------------------------------
*/

// Route::get('/login', [SSOController::class, 'redirect'])->name('login');  // ✅ perbaiki dari app() pemanggilan langsung
// Route::get('/auth/callback', [SSOController::class, 'callback'])->name('sso.callback');
Route::get('/login', function () {
    return view('auth.login'); // kita buat file-nya nanti
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->route('after.sso');
    }

    return back()->withErrors([
        'email' => 'Email atau password salah',
    ]);
})->name('login.submit');

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

/*
|--------------------------------------------------------------------------
| AFTER SSO LOGIN REDIRECT
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->get('/after-sso', function () {
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

/*
|--------------------------------------------------------------------------
| DASHBOARD PER ROLE
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::view('/super-admin/dashboard', 'super-admin.dashboard')->name('super-admin.dashboard');
    Route::view('/tim-ppk/dashboard', 'tim-ppk.dashboard')->name('tim-ppk.dashboard');
    Route::view('/instalasi/dashboard', 'instalasi.dashboard')->name('instalasi.dashboard');

    // redirect fallback dashboard
    Route::redirect('/dashboard', '/after-sso')->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| USER MANAGEMENT
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('akun')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('akun.index');
    Route::get('/{id}', [UserController::class, 'show'])->name('akun.show');
    Route::get('/{id}/edit', [UserController::class, 'edit'])->name('akun.edit');
    Route::put('/{id}', [UserController::class, 'update'])->name('akun.update');

    Route::get('/profile/me', [UserController::class, 'profile'])->name('akun.profile');
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

    // ✅ PEMESANAN
    Route::prefix('pemesanan')->group(function () {
        Route::get('/', [PemesananController::class, 'index'])->name('pemesanan.index');
        Route::get('/create', [PemesananController::class, 'create'])->name('pemesanan.create');
        Route::post('/store', [PemesananController::class, 'store'])->name('pemesanan.store');
        Route::get('/{id}', [PemesananController::class, 'show'])->name('pemesanan.show');
        Route::get('/{id}/download', [PemesananController::class, 'downloadStruk'])->name('pemesanan.download');
    });

    // ✅ PENERIMAAN
    Route::prefix('penerimaan')->group(function () {
        Route::get('/', [PenerimaanController::class, 'index'])->name('penerimaan.index');
        Route::get('/{id}', [PenerimaanController::class, 'show'])->name('penerimaan.show');
        Route::post('/{id}/update-status', [PenerimaanController::class, 'updateStatus'])->name('penerimaan.updateStatus');
        Route::post('/{id}/confirm', [PenerimaanController::class, 'confirm'])->name('penerimaan.confirm');
    });

    // ✅ BAST
    Route::prefix('bast')->group(function () {
        Route::get('/{id}/download', [BastController::class, 'download'])->name('bast.download');
        Route::post('/{id}/upload', [BastController::class, 'upload'])->name('bast.upload');
    });

    // ✅ ITEM/STOK
    Route::prefix('stok')->group(function () {
        Route::get('/', [ItemController::class, 'index'])->name('stok.index');
    });

    // ✅ NOTIFIKASI
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
});

// JABATAN MANAGEMENT
Route::middleware(['auth'])->prefix('jabatan')->group(function () {
    Route::get('/', [JabatanController::class, 'index'])->name('jabatan.index');
    Route::get('/create', [JabatanController::class, 'create'])->name('jabatan.create');
    Route::post('/store', [JabatanController::class, 'store'])->name('jabatan.store');
    Route::get('/{id}/edit', [JabatanController::class, 'edit'])->name('jabatan.edit');
    Route::put('/{id}', [JabatanController::class, 'update'])->name('jabatan.update');
    Route::delete('/{id}', [JabatanController::class, 'destroy'])->name('jabatan.destroy');
});
