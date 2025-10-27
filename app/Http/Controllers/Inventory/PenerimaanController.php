<?php
namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PenerimaanService;

/**
 * @method void middleware(string|array $middleware, array $options = [])
 */

class PenerimaanController extends Controller
{
    protected PenerimaanService $service;

    public function __construct(PenerimaanService $service)
    {
        $this->service = $service;
        $this->middleware('auth');
    }

    // Simpan penerimaan saat barang datang
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

        $penerimaan = $this->service->createPenerimaan($data);
        return response()->json($penerimaan);
    }

    // Set layak/tidak layak untuk satu detail
    public function setLayak(Request $request, $idDetail)
    {
        $request->validate(['is_layak' => 'required|boolean']);
        $resp = $this->service->setDetailLayak($idDetail, $request->is_layak);
        return response()->json($resp);
    }

    public function show($id)
    {
        return response()->json($this->service->getPenerimaan($id));
    }
}
