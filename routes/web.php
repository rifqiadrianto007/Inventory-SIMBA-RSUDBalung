<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inventory\PemesananController;
use App\Http\Controllers\Inventory\PenerimaanController;
use App\Http\Controllers\Inventory\BastController;
use App\Http\Controllers\Inventory\ItemController;
use App\Http\Controllers\Inventory\NotifikasiController;

// ==================== HALAMAN UTAMA ====================
Route::get('/', function () {
    return view('welcome');
})->name('dashboard');

// ==================== PEMESANAN (Bagian Instalasi / PPK) ====================
Route::prefix('pemesanan')->group(function () {
    Route::get('/', [PemesananController::class, 'index'])->name('pemesanan.index');
    Route::get('/create', [PemesananController::class, 'create'])->name('pemesanan.create');
    Route::post('/store', [PemesananController::class, 'store'])->name('pemesanan.store');
    Route::get('/{id}', [PemesananController::class, 'show'])->name('pemesanan.show');
    Route::get('/{id}/download', [PemesananController::class, 'downloadStruk'])->name('pemesanan.download');
});

// ==================== PENERIMAAN (Bagian Gudang / Teknisi) ====================
Route::prefix('penerimaan')->group(function () {
    Route::get('/', [PenerimaanController::class, 'index'])->name('penerimaan.index');
    Route::get('/{id}', [PenerimaanController::class, 'show'])->name('penerimaan.show');
    Route::post('/{id}/update-status', [PenerimaanController::class, 'updateStatus'])->name('penerimaan.updateStatus');
    Route::post('/{id}/confirm', [PenerimaanController::class, 'confirm'])->name('penerimaan.confirm');
});

// ==================== BAST (Berita Acara Serah Terima) ====================
Route::prefix('bast')->group(function () {
    Route::get('/{id}/download', [BastController::class, 'download'])->name('bast.download');
    Route::post('/{id}/upload', [BastController::class, 'upload'])->name('bast.upload');
});

// ==================== STOK BARANG ====================
Route::prefix('stok')->group(function () {
    Route::get('/', [ItemController::class, 'index'])->name('stok.index');
});

// ==================== NOTIFIKASI ====================
Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
