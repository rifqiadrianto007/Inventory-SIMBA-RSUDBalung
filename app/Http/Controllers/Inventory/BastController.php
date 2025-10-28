<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PenerimaanService;
use App\Models\Bast;
use Illuminate\Support\Facades\Storage;

/**
 * @method void middleware(string|array $middleware, array $options = [])
 */

class BastController extends Controller
{
    protected PenerimaanService $penerimaanService;

    public function __construct(PenerimaanService $penerimaanService)
    {
        $this->penerimaanService = $penerimaanService;
    }

    /**
     * 📥 Mengunduh file BAST
     */
    public function download($id)
    {
        $bast = $this->penerimaanService->getBast($id);
        if (!$bast || !Storage::disk('public')->exists($bast->file_path)) {
            return back()->with('error', 'File BAST tidak ditemukan.');
        }

        return response()->download(storage_path("app/public/{$bast->file_path}"));
    }

    /**
     * 📤 Upload BAST manual oleh admin gudang
     */
    public function upload(Request $request, $id)
    {
        $request->validate([
            'file_bast' => 'required|file|mimes:pdf|max:2048',
        ]);

        $path = $request->file('file_bast')->store('bast', 'public');

        Bast::create([
            'id_penerimaan' => $id,
            'no_surat' => 'BAST-MANUAL-' . now()->format('YmdHis'),
            'file_path' => $path,
            'deskripsi' => 'Upload manual oleh admin gudang',
        ]);

        return back()->with('success', 'BAST berhasil diupload.');
    }
}
