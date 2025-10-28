<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PemesananService;

class PemesananController extends Controller
{
    protected PemesananService $service;

    public function __construct(PemesananService $service)
    {
        $this->service = $service;
        // $this->middleware('auth'); // ✅ ini akan berfungsi jika base Controller sudah benar
    }

    /**
     * 📋 Menampilkan daftar semua pemesanan
     */
    public function index()
    {
        $data = $this->service->getAllPemesanan();
        return view('inventory.pemesanan.index', compact('data'));
    }

    /**
     * ➕ Form tambah pemesanan
     */
    public function create()
    {
        return view('inventory.pemesanan.create');
    }

    /**
     * 🧾 Simpan data pemesanan baru (klik tombol +)
     */
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

        // Kirim ke service untuk diproses
        $result = $this->service->createPemesanan($data);

        if (isset($result['error'])) {
            return back()->with('error', 'Stok barang tidak mencukupi untuk beberapa item.');
        }

        return redirect()
            ->route('pemesanan.index')
            ->with('success', 'Pemesanan berhasil dibuat dan disetujui otomatis.');
    }

    /**
     * 🔍 Lihat detail pemesanan
     */
    public function show($id)
    {
        $pemesanan = $this->service->getPemesanan($id);
        return view('inventory.pemesanan.show', compact('pemesanan'));
    }

    /**
     * 📥 Unduh struk pemesanan (PDF)
     */
    public function downloadStruk($id)
    {
        $path = $this->service->downloadStruk($id);

        if (!$path || !isset($path['file_url'])) {
            return back()->with('error', 'Struk pemesanan tidak ditemukan.');
        }

        return redirect($path['file_url']);
    }
}
