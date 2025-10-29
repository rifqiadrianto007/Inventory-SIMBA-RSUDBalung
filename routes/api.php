<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\PemesananApiController;
use App\Http\Controllers\API\PenerimaanApiController;
use App\Http\Controllers\API\BastApiController;
use App\Http\Controllers\API\ItemApiController;
use App\Http\Controllers\API\NotifikasiApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Semua endpoint API yang bisa diakses oleh frontend.
| Endpoint ini menggunakan autentikasi Sanctum (token-based).
| Response selalu dalam format JSON.
|
*/

// 🔹 Endpoint publik (tanpa autentikasi)
Route::get('/status', function () {
    return response()->json(['status' => 'API aktif', 'timestamp' => now()]);
});

// 🔒 Endpoint dengan autentikasi Sanctum
Route::middleware('auth:sanctum')->group(function () {

    // ==================== PEMESANAN ====================
    Route::prefix('pemesanan')->group(function () {
        Route::get('/', [PemesananApiController::class, 'index']);           // List semua pemesanan
        Route::get('/{id}', [PemesananApiController::class, 'show']);        // Detail pemesanan
        Route::post('/', [PemesananApiController::class, 'store']);          // Buat pemesanan baru
        Route::get('/{id}/download', [PemesananApiController::class, 'downloadStruk']); // Unduh struk
    });

    // ==================== PENERIMAAN ====================
    Route::prefix('penerimaan')->group(function () {
        Route::get('/', [PenerimaanApiController::class, 'index']);           // List penerimaan
        Route::get('/{id}', [PenerimaanApiController::class, 'show']);        // Detail penerimaan
        Route::post('/', [PenerimaanApiController::class, 'store']);          // Input penerimaan baru
        Route::post('/{id}/set-layak', [PenerimaanApiController::class, 'setLayak']); // Update kelayakan
        Route::post('/{id}/confirm', [PenerimaanApiController::class, 'confirm']);    // Konfirmasi penerimaan
    });

    // ==================== BAST ====================
    Route::prefix('bast')->group(function () {
        Route::get('/{id}/download', [BastApiController::class, 'download']); // Unduh file BAST
        Route::post('/', [BastApiController::class, 'store']);                // Buat BAST manual
    });

    // ==================== STOK / ITEM ====================
    Route::prefix('item')->group(function () {
        Route::get('/', [ItemApiController::class, 'index']);                 // List semua barang
        Route::get('/{id}', [ItemApiController::class, 'show']);              // Detail barang
        Route::put('/{id}/update-stock', [ItemApiController::class, 'updateStock']); // Update stok
    });

    // ==================== NOTIFIKASI ====================
    Route::prefix('notifikasi')->group(function () {
        Route::get('/', [NotifikasiApiController::class, 'index']);           // List notifikasi user
        Route::post('/{id}/read', [NotifikasiApiController::class, 'markRead']); // Tandai sebagai dibaca
    });

    // ==================== USER PROFILE ====================
    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    });
});
