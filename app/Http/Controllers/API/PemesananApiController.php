<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PemesananService;
use Illuminate\Support\Facades\Log;

class PemesananApiController extends Controller
{
    protected PemesananService $service;

    public function __construct(PemesananService $service)
    {
        $this->service = $service;
    }

    // ✅ Ambil semua pemesanan
    public function index()
    {
        try {
            $data = $this->service->getAllPemesanan();
            return response()->json($data, 200);
        } catch (\Throwable $e) {
            Log::error('Gagal mengambil daftar pemesanan', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal memuat data pemesanan'], 500);
        }
    }

    // ✅ Ambil detail pemesanan
    public function show($id)
    {
        try {
            $data = $this->service->getPemesanan($id);
            return response()->json($data, 200);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Data pemesanan tidak ditemukan'], 404);
        }
    }

    // ✅ Simpan pemesanan baru
    public function store(Request $request)
    {
        $data = $request->validate([
            'sso_user_id' => 'nullable|integer',
            'asal_instalasi' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.id_item' => 'required|exists:item,id_item',
            'items.*.id_satuan' => 'required|exists:satuan,id_satuan',
            'items.*.volume' => 'required|numeric|min:0.01',
        ]);

        $result = $this->service->createPemesanan($data);
        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], 400);
        }

        return response()->json([
            'message' => 'Pemesanan berhasil dibuat',
            'data' => $result,
        ], 201);
    }

    // ✅ Unduh struk
    public function downloadStruk($id)
    {
        $path = $this->service->downloadStruk($id);
        if (isset($path['error'])) {
            return response()->json($path, 404);
        }
        return response()->json($path, 200);
    }
}
