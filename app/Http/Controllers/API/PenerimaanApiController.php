<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PenerimaanService;
use Illuminate\Support\Facades\Log;

class PenerimaanApiController extends Controller
{
    protected PenerimaanService $service;

    public function __construct(PenerimaanService $service)
    {
        $this->service = $service;
    }

    /**
     * 📋 Ambil semua penerimaan barang
     */
    public function index()
    {
        try {
            $data = $this->service->getAllPenerimaan();
            return response()->json($data, 200);
        } catch (\Throwable $e) {
            Log::error('Gagal memuat daftar penerimaan', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal memuat data penerimaan'], 500);
        }
    }

    /**
     * 🔍 Ambil detail penerimaan
     */
    public function show($id)
    {
        try {
            $data = $this->service->getPenerimaan($id);
            return response()->json($data, 200);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Penerimaan tidak ditemukan'], 404);
        }
    }

    /**
     * 🧾 Simpan penerimaan baru
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'sso_user_id' => 'nullable|integer',
            'tanggal_penerimaan' => 'required|date',
            'total_harga' => 'nullable|numeric',
            'details' => 'required|array|min:1',
            'details.*.id_item' => 'required|exists:item,id_item',
            'details.*.id_category' => 'required|exists:category,id_category',
            'details.*.volume' => 'required|numeric|min:0.01',
            'details.*.id_satuan' => 'required|exists:satuan,id_satuan',
            'details.*.harga' => 'required|numeric|min:0',
        ]);

        try {
            $result = $this->service->createPenerimaan($data);
            return response()->json([
                'message' => 'Penerimaan berhasil disimpan',
                'data' => $result
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Gagal menyimpan penerimaan', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal menyimpan penerimaan'], 500);
        }
    }

    /**
     * ✅ Set kelayakan barang
     */
    public function setLayak(Request $request, $id)
    {
        $request->validate(['is_layak' => 'required|boolean']);

        try {
            $result = $this->service->setDetailLayak($id, $request->is_layak);
            return response()->json($result, 200);
        } catch (\Throwable $e) {
            Log::error('Gagal memperbarui status kelayakan', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal memperbarui status kelayakan'], 500);
        }
    }

    /**
     * 🧩 Konfirmasi penerimaan oleh tim teknis
     */
    public function confirm($id)
    {
        try {
            $result = $this->service->confirmPenerimaan($id);
            return response()->json([
                'message' => 'Penerimaan telah dikonfirmasi',
                'data' => $result
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Gagal konfirmasi penerimaan', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal mengonfirmasi penerimaan'], 500);
        }
    }
}
