<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PemesananService;

class PemesananApiController extends Controller
{
    protected PemesananService $service;

    public function __construct(PemesananService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return response()->json($this->service->getAllPemesanan());
    }

    public function show($id)
    {
        return response()->json($this->service->getPemesanan($id));
    }

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
        return response()->json($result);
    }

    public function downloadStruk($id)
    {
        return response()->json($this->service->downloadStruk($id));
    }
}
