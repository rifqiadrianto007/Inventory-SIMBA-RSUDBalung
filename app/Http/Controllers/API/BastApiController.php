<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PenerimaanService;

class BastApiController extends Controller
{
    protected PenerimaanService $service;

    public function __construct(PenerimaanService $service)
    {
        $this->service = $service;
    }

    public function download($id)
    {
        $bast = $this->service->getBast($id);
        if (!$bast) {
            return response()->json(['error' => 'BAST tidak ditemukan'], 404);
        }

        return response()->json(['file_url' => asset("storage/{$bast->file_path}")]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_penerimaan' => 'required|exists:penerimaan,id_penerimaan',
            'no_surat' => 'required|string',
            'deskripsi' => 'nullable|string',
        ]);

        $result = $this->service->createBastManual($data);
        return response()->json($result);
    }
}
