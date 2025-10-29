<?php

namespace App\Services;

use App\Models\Pemesanan;
use App\Models\DetailPemesanan;
use App\Models\Item;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\PdfService;
use App\Services\LogService;
use App\Services\NotificationService;

class PemesananService
{
    protected PdfService $pdf;
    protected LogService $log;
    protected NotificationService $notify;

    public function __construct(PdfService $pdf, LogService $log, NotificationService $notify)
    {
        $this->pdf = $pdf;
        $this->log = $log;
        $this->notify = $notify;
    }

    /**
     * 🔍 Ambil semua data pemesanan
     */
    public function getAllPemesanan()
    {
        return Pemesanan::with('details.satuan', 'user')
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * 📦 Buat pemesanan baru
     * Otomatis disetujui bila stok cukup.
     * Membuat detail, mengurangi stok, dan generate struk PDF.
     */
    public function createPemesanan(array $data)
    {
        DB::beginTransaction();

        try {
            $pemesanan = Pemesanan::create([
                'sso_user_id'    => $data['sso_user_id'] ?? null,
                'asal_instalasi' => $data['asal_instalasi'] ?? '-',
                'status'         => 'pending',
            ]);

            $stokKurang = [];
            $detailList = [];

            foreach ($data['items'] as $item) {
                $barang = Item::lockForUpdate()->findOrFail($item['id_item']);

                if ($barang->stock_item < $item['volume']) {
                    $stokKurang[] = $barang->name;
                    $statusItem = 'stok_kurang';
                } else {
                    $statusItem = 'ok';
                    $barang->decrement('stock_item', $item['volume']);
                }

                $detailList[] = [
                    'id_pemesanan' => $pemesanan->id_pemesanan,
                    'id_satuan'    => $item['id_satuan'],
                    'volume'       => $item['volume'],
                    'status_item'  => $statusItem,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ];
            }

            DetailPemesanan::insert($detailList);

            // Jika semua stok mencukupi → langsung approve
            if (empty($stokKurang)) {
                $pemesanan->status = 'approved';
                $msg = "Pemesanan #{$pemesanan->id_pemesanan} disetujui otomatis.";
            } else {
                $pemesanan->status = 'partial';
                $msg = "Beberapa barang tidak cukup stok: " . implode(', ', $stokKurang);
            }

            $pemesanan->save();

            // Generate PDF (tetap muncul meski stok sebagian kurang)
            $noStruk  = 'PO-' . date('Ymd') . '-' . Str::random(5);
            $fileName = "struk_pemesanan_{$noStruk}_{$pemesanan->id_pemesanan}.pdf";
            $filePath = "pemesanan/{$fileName}";

            $pdfData = [
                'no_struk'   => $noStruk,
                'pemesanan'  => $pemesanan->load('details.satuan'),
                'tanggal'    => now()->format('d M Y'),
                'instalasi'  => $pemesanan->asal_instalasi,
                'stok_kurang'=> $stokKurang,
            ];

            $this->pdf->generate('pdf.pemesanan_struk', $pdfData, $filePath);

            // Log & Notifikasi
            $this->log->record('create_pemesanan', 'pemesanan', $msg);
            $this->notify->send(
                $pemesanan->sso_user_id ?? 0,
                'Status Pemesanan',
                $msg,
                "/pemesanan/{$pemesanan->id_pemesanan}"
            );

            DB::commit();

            return [
                'ok' => true,
                'pemesanan' => $pemesanan->load('details.satuan'),
                'file_url'  => asset("storage/{$filePath}"),
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 🔍 Ambil data pemesanan berdasarkan ID
     */
    public function getPemesanan($id)
    {
        return Pemesanan::with('details.satuan', 'user')->findOrFail($id);
    }

    /**
     * 📄 Download struk pemesanan
     */
    public function downloadStruk($id)
    {
        $p = Pemesanan::findOrFail($id);
        $pattern = "pemesanan/struk_pemesanan_*_{$p->id_pemesanan}.pdf";
        $files = glob(storage_path("app/public/{$pattern}"));

        if (count($files) > 0) {
            $fileName = basename($files[0]);
            return ['file_url' => asset("storage/pemesanan/{$fileName}")];
        }

        return ['error' => 'File struk tidak ditemukan'];
    }
}
