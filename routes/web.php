<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Inventory\PemesananController;
use App\Http\Controllers\Inventory\PenerimaanController;
use App\Http\Controllers\Inventory\BastController;
use App\Http\Controllers\Inventory\ItemController;
use App\Http\Controllers\Inventory\NotifikasiController;
use App\Http\Controllers\LogActivityController;

/*
|--------------------------------------------------------------------------
| WEB ROUTES
|--------------------------------------------------------------------------
|
| Semua rute aplikasi web dengan dukungan session & CSRF.
| Cocok untuk sistem inventory berbasis Laravel + Inertia/Blade.
|
*/

// ==================== DASHBOARD ====================
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');


// ==================== PEMESANAN (Bagian Instalasi / PPK) ====================
// Menampilkan daftar pemesanan
Route::get('/pemesanan', [PemesananController::class, 'index'])->name('pemesanan.index');

// Form tambah pemesanan (icon “+”)
Route::get('/pemesanan/create', [PemesananController::class, 'create'])->name('pemesanan.create');

// Simpan pemesanan baru
Route::post('/pemesanan', [PemesananController::class, 'store'])->name('pemesanan.store');

// Lihat detail pemesanan
Route::get('/pemesanan/{id}', [PemesananController::class, 'show'])->name('pemesanan.show');

// Unduh struk pemesanan (PDF)
Route::get('/pemesanan/{id}/download', [PemesananController::class, 'downloadStruk'])->name('pemesanan.download');


// ==================== PENERIMAAN (Bagian Gudang / Teknisi) ====================
// Daftar penerimaan barang
Route::get('/penerimaan', [PenerimaanController::class, 'index'])->name('penerimaan.index');

// Detail penerimaan tertentu
Route::get('/penerimaan/{id}', [PenerimaanController::class, 'show'])->name('penerimaan.show');

// Simpan penerimaan baru (barang datang)
Route::post('/penerimaan', [PenerimaanController::class, 'store'])->name('penerimaan.store');

// Update kelayakan barang
Route::post('/penerimaan/{id}/set-layak', [PenerimaanController::class, 'setLayak'])->name('penerimaan.setLayak');


// ==================== BAST (Berita Acara Serah Terima) ====================
// Membuat BAST manual (oleh kepala gudang)
Route::post('/bast/store', [BastController::class, 'store'])->name('bast.store');

// Mengunduh file BAST
Route::get('/bast/{id}/download', [BastController::class, 'download'])->name('bast.download');


// ==================== STOK BARANG (Admin Gudang) ====================
// Melihat stok barang
Route::get('/stok', [ItemController::class, 'index'])->name('stok.index');

// Mengubah stok barang
Route::post('/stok/{id}/update', [ItemController::class, 'updateStock'])->name('stok.updateStock');


// ==================== NOTIFIKASI ====================
Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
Route::post('/notifikasi/{id}/read', [NotifikasiController::class, 'markRead'])->name('notifikasi.markRead');
Route::post('/notifikasi/read-all', [NotifikasiController::class, 'markAllRead'])->name('notifikasi.markAllRead');


// ==================== LOG AKTIVITAS ====================
Route::get('/log', [LogActivityController::class, 'index'])->name('log.index');
