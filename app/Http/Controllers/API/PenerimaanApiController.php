<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PenerimaanService;

class PenerimaanApiController extends Controller
{
    protected PenerimaanService $service;

    public function __construct(PenerimaanService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return response()->json($this->service->getAllPenerimaan());
    }

    public function show($id)
    {
        return response()->json($this->service->getPenerimaan($id));
    }

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

        return response()->json($this->service->createPenerimaan($data));
    }

    public function setLayak(Request $request, $id)
    {
        $request->validate(['is_layak' => 'required|boolean']);
        return response()->json($this->service->setDetailLayak($id, $request->is_layak));
    }

    public function confirm($id)
    {
        return response()->json($this->service->confirmPenerimaan($id));
    }
}
