<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PemesananController;
use App\Http\Controllers\PenerimaanController;
use App\Http\Controllers\BASTController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LogActivityController;
use App\Http\Controllers\NotifikasiController;

Route::get('/', function () {
    return Inertia::render('Home', [
        'title' => 'Halo dari Laravel + React + Inertia'
    ]);
});

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Semua rute web (dengan session & CSRF protection) didefinisikan di sini.
| Rute ini cocok untuk aplikasi inventory berbasis website internal.
|
*/

// ==================== DASHBOARD ====================
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');


// ==================== PEMESANAN (Bagian Instalasi / PPK) ====================
// Melihat daftar barang dan melakukan pemesanan
Route::get('/pemesanan', [PemesananController::class, 'index'])->name('pemesanan.index');

// Membuat data belanja (klik icon "+")
Route::get('/pemesanan/create', [PemesananController::class, 'create'])->name('pemesanan.create');
Route::post('/pemesanan/store', [PemesananController::class, 'store'])->name('pemesanan.store');

// Mengedit data belanja
Route::get('/pemesanan/{id}/edit', [PemesananController::class, 'edit'])->name('pemesanan.edit');
Route::put('/pemesanan/{id}', [PemesananController::class, 'update'])->name('pemesanan.update');

// Menghapus data belanja
Route::delete('/pemesanan/{id}', [PemesananController::class, 'destroy'])->name('pemesanan.destroy');

// Melihat status pemesanan
Route::get('/pemesanan/status', [PemesananController::class, 'status'])->name('pemesanan.status');


// ==================== PENERIMAAN (Bagian Gudang / Teknisi) ====================
// Melihat daftar penerimaan barang
Route::get('/penerimaan', [PenerimaanController::class, 'index'])->name('penerimaan.index');

// Melihat detail penerimaan
Route::get('/penerimaan/{id}', [PenerimaanController::class, 'show'])->name('penerimaan.show');

// Upload BAST (oleh kepala gudang)
Route::get('/penerimaan/{id}/upload', [PenerimaanController::class, 'uploadForm'])->name('penerimaan.uploadForm');
Route::post('/penerimaan/{id}/upload', [PenerimaanController::class, 'uploadBAST'])->name('penerimaan.uploadBAST');

// Mengubah status kelayakan barang (oleh teknisi)
Route::post('/penerimaan/{id}/update-status', [PenerimaanController::class, 'updateStatus'])->name('penerimaan.updateStatus');

// Konfirmasi penerimaan barang (tim teknis)
Route::post('/penerimaan/{id}/confirm', [PenerimaanController::class, 'confirm'])->name('penerimaan.confirm');


// ==================== BAST (Berita Acara Serah Terima) ====================
// Membuat BAST (oleh kepala gudang)
Route::get('/bast/create/{id_penerimaan}', [BASTController::class, 'create'])->name('bast.create');
Route::post('/bast/store', [BASTController::class, 'store'])->name('bast.store');

// Mengunduh file BAST
Route::get('/bast/{id}/download', [BASTController::class, 'download'])->name('bast.download');


// ==================== STOK BARANG (Admin Gudang) ====================
// Melihat stok
Route::get('/stok', [ItemController::class, 'index'])->name('stok.index');

// Mengedit stok barang
Route::get('/stok/{id}/edit', [ItemController::class, 'edit'])->name('stok.edit');
Route::put('/stok/{id}', [ItemController::class, 'update'])->name('stok.update');


// ==================== LOG AKTIVITAS DAN NOTIFIKASI ====================
Route::get('/log', [LogActivityController::class, 'index'])->name('log.index');
Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
