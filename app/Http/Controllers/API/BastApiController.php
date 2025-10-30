<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PenerimaanService;
use Illuminate\Support\Facades\Log;

class BastApiController extends Controller
{
    protected PenerimaanService $service;

    public function __construct(PenerimaanService $service)
    {
        $this->service = $service;
    }

    /**
     * 🔽 Unduh file BAST
     */
    public function download($id)
    {
        try {
            $bast = $this->service->getBast($id);

            if (!$bast) {
                return response()->json(['error' => 'BAST tidak ditemukan'], 404);
            }

            return response()->json([
                'message' => 'File BAST berhasil ditemukan',
                'file_url' => asset("storage/{$bast->file_path}")
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Gagal mengunduh BAST', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Terjadi kesalahan saat mengambil data BAST'], 500);
        }
    }

    /**
     * 🧾 Buat BAST manual (oleh Kepala Gudang)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'id_penerimaan' => 'required|exists:penerimaan,id_penerimaan',
            'no_surat' => 'required|string',
            'deskripsi' => 'nullable|string',
        ]);

        try {
            $result = $this->service->createBastManual($data);
            return response()->json([
                'message' => 'BAST berhasil dibuat',
                'data' => $result
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Gagal membuat BAST manual', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal membuat BAST manual'], 500);
        }
    }
}
