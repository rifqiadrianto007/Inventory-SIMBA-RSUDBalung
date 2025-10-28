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
    }

    /**
     * 📦 Menampilkan daftar penerimaan barang
     */
    public function index()
    {
        $data = $this->service->getAllPenerimaan();
        return view('inventory.penerimaan.index', compact('data'));
    }

    /**
     * 🔍 Menampilkan detail penerimaan barang
     */
    public function show($id)
    {
        $penerimaan = $this->service->getPenerimaan($id);
        return view('inventory.penerimaan.show', compact('penerimaan'));
    }

    /**
     * ✅ Mengubah status kelayakan barang
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate(['is_layak' => 'required|boolean']);

        $result = $this->service->setDetailLayak($id, $request->is_layak);
        $message = $request->is_layak
            ? 'Barang layak digunakan.'
            : 'Barang tidak layak digunakan.';

        return back()->with('success', $message);
    }

    /**
     * 🧾 Konfirmasi pengecekan data belanja
     */
    public function confirm($id)
    {
        $result = $this->service->confirmPenerimaan($id);

        return back()->with('success', 'Anda berhasil mengecek barang belanja.');
    }
}
