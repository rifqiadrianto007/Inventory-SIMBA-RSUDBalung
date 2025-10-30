<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Import semua controller API
use App\Http\Controllers\API\PemesananApiController;
use App\Http\Controllers\API\PenerimaanApiController;
use App\Http\Controllers\API\BastApiController;
use App\Http\Controllers\API\ItemApiController;
use App\Http\Controllers\API\NotifikasiApiController;

// ✅ Cek status API (tanpa login)
Route::get('/v1/status', function () {
    return response()->json([
        'status' => 'API aktif',
        'timestamp' => now(),
        'version' => 'v1.0.0'
    ]);
});

// ===================================================================
// 📦 PEMESANAN
// ===================================================================
Route::prefix('v1/pemesanan')->group(function () {
    Route::get('/', [PemesananApiController::class, 'index']);               // GET semua pemesanan
    Route::get('/{id}', [PemesananApiController::class, 'show']);            // GET detail pemesanan
    Route::post('/', [PemesananApiController::class, 'store']);              // POST buat pemesanan
    Route::get('/{id}/download', [PemesananApiController::class, 'downloadStruk']); // GET unduh struk
});

// ===================================================================
// 📥 PENERIMAAN
// ===================================================================
Route::prefix('v1/penerimaan')->group(function () {
    Route::get('/', [PenerimaanApiController::class, 'index']);              // GET daftar penerimaan
    Route::get('/{id}', [PenerimaanApiController::class, 'show']);           // GET detail penerimaan
    Route::post('/', [PenerimaanApiController::class, 'store']);             // POST buat penerimaan baru
    Route::post('/{id}/set-layak', [PenerimaanApiController::class, 'setLayak']); // POST ubah kelayakan
    Route::post('/{id}/confirm', [PenerimaanApiController::class, 'confirm']);    // POST konfirmasi
});

// ===================================================================
// 📑 BAST (Berita Acara Serah Terima)
// ===================================================================
Route::prefix('v1/bast')->group(function () {
    Route::get('/{id}/download', [BastApiController::class, 'download']);    // GET unduh file BAST
    Route::post('/', [BastApiController::class, 'store']);                   // POST buat BAST manual
});

// ===================================================================
// 📦 ITEM / STOK
// ===================================================================
Route::prefix('v1/item')->group(function () {
    Route::get('/', [ItemApiController::class, 'index']);                    // GET semua item
    Route::get('/{id}', [ItemApiController::class, 'show']);                 // GET detail item
    Route::put('/{id}/update-stock', [ItemApiController::class, 'updateStock']); // PUT update stok
});

// ===================================================================
// 🔔 NOTIFIKASI
// ===================================================================
Route::prefix('v1/notifikasi')->group(function () {
    Route::get('/', [NotifikasiApiController::class, 'index']);              // GET semua notifikasi
    Route::post('/{id}/read', [NotifikasiApiController::class, 'markRead']); // POST tandai dibaca

    // 🔹 (Opsional) Endpoint testing frontend
    Route::post('/test', function (Request $request) {
        return response()->json([
            'ok' => true,
            'message' => 'Endpoint notifikasi API siap digunakan!',
            'example' => [
                'GET /api/v1/notifikasi?user_id=1',
                'POST /api/v1/notifikasi/5/read'
            ]
        ]);
    });
});

// ===================================================================
// 👤 USER PROFILE (opsional untuk SSO)
// ===================================================================
Route::get('/v1/user', function (Request $request) {
    return response()->json([
        'ok' => true,
        'user' => $request->user() ?? 'Guest',
    ]);
});
