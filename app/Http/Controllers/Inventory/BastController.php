<?php
namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PdfService;
use App\Services\PenerimaanService;

/**
 * @method void middleware(string|array $middleware, array $options = [])
 */

class BastController extends Controller
{
    protected PdfService $pdf;
    protected PenerimaanService $penerimaanService;

    public function __construct(PdfService $pdf, PenerimaanService $penerimaanService)
    {
        $this->pdf = $pdf;
        $this->penerimaanService = $penerimaanService;
        $this->middleware('auth');
    }

    // Unduh BAST (file yang sudah dibuat)
    public function download($id)
    {
        $bast = $this->penerimaanService->getBast($id);
        if (!$bast) return response()->json(['error'=>'BAST tidak ditemukan'],404);
        return response()->json(['file_url' => asset("storage/{$bast->file_path}")]);
    }

    // (opsional) buat BAST manual - kepala gudang
    public function store(Request $request)
    {
        $data = $request->validate([
            'id_penerimaan' => 'required|exists:penerimaan,id_penerimaan',
            'no_surat' => 'required|string',
            'deskripsi' => 'nullable|string',
            'staker' => 'nullable|array'
        ]);
        $bast = $this->penerimaanService->createBastManual($data);
        return response()->json($bast);
    }
}
