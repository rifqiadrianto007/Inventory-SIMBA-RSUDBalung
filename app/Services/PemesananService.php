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
        return Pemesanan::with('details.item', 'details.satuan', 'user')
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * 📦 Buat pemesanan baru
     * Otomatis disetujui bila stok cukup.
     * Menyimpan status stok, mengurangi stok, dan generate struk PDF.
     */
    public function createPemesanan(array $data)
    {
        DB::beginTransaction();

        try {
            // 1️⃣ Buat pemesanan utama
            $pemesanan = Pemesanan::create([
                'sso_user_id'    => $data['sso_user_id'] ?? null,
                'asal_instalasi' => $data['asal_instalasi'] ?? '-',
                'status'         => 'pending',
            ]);

            $stokKurang = [];
            $detailList = [];

            // 2️⃣ Periksa stok tiap item
            foreach ($data['items'] as $item) {
                $barang = Item::findOrFail($item['id_item']);

                // tentukan status stok
                $statusItem = $barang->stock_item < $item['volume']
                    ? 'stok kurang'
                    : 'ok';

                // simpan detail
                $detailList[] = [
                    'id_pemesanan' => $pemesanan->id_pemesanan,
                    'id_item'      => $item['id_item'],
                    'id_satuan'    => $item['id_satuan'],
                    'volume'       => $item['volume'],
                    'status_item'  => $statusItem,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ];

                // jika stok cukup → kurangi stok
                if ($statusItem === 'ok') {
                    $barang->stock_item -= $item['volume'];
                    $barang->save();
                } else {
                    $stokKurang[] = $barang->name;
                }
            }

            // 3️⃣ Simpan semua detail
            DetailPemesanan::insert($detailList);

            // 4️⃣ Jika stok tidak cukup, pemesanan dibatalkan
            if (!empty($stokKurang)) {
                $pemesanan->status = 'rejected';
                $pemesanan->save();

                $this->notify->send(
                    $pemesanan->sso_user_id ?? 0,
                    'Pemesanan Gagal',
                    'Pemesanan dibatalkan karena stok tidak mencukupi untuk: ' . implode(', ', $stokKurang),
                    "/pemesanan/{$pemesanan->id_pemesanan}"
                );

                DB::commit();

                return [
                    'error' => 'Stok tidak mencukupi: ' . implode(', ', $stokKurang),
                    'status' => 'rejected',
                ];
            }

            // 5️⃣ Semua stok cukup → status approved
            $pemesanan->status = 'approved';
            $pemesanan->save();

            // 6️⃣ Generate struk PDF
            $noStruk  = 'PO-' . date('Ymd') . '-' . Str::random(5);
            $fileName = "struk_pemesanan_{$noStruk}_{$pemesanan->id_pemesanan}.pdf";
            $filePath = "pemesanan/{$fileName}";

            $pdfData = [
                'no_struk'   => $noStruk,
                'pemesanan'  => $pemesanan->load('details.item', 'details.satuan'),
                'tanggal'    => now()->format('d M Y'),
                'instalasi'  => $pemesanan->asal_instalasi,
            ];

            $this->pdf->generate('pdf.pemesanan_struk', $pdfData, $filePath);

            // 7️⃣ Catat log
            $this->log->record(
                'create_pemesanan',
                'pemesanan',
                "Pemesanan #{$pemesanan->id_pemesanan} disetujui otomatis"
            );

            // 8️⃣ Kirim notifikasi sukses
            $this->notify->send(
                $pemesanan->sso_user_id ?? 0,
                'Pemesanan Disetujui',
                "Pemesanan #{$pemesanan->id_pemesanan} berhasil dibuat dan disetujui otomatis.",
                "/pemesanan/{$pemesanan->id_pemesanan}"
            );

            DB::commit();

            return [
                'ok'       => true,
                'status'   => 'approved',
                'pemesanan'=> $pemesanan->load('details.item', 'details.satuan'),
                'file_url' => asset("storage/{$filePath}"),
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
        return Pemesanan::with('details.item', 'details.satuan', 'user')->findOrFail($id);
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
