<?php
namespace App\Services;

use App\Models\Pemesanan;
use App\Models\DetailPemesanan;
use App\Services\InventoryService;
use App\Services\PdfService;
use App\Services\LogService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;

class PemesananService
{
    protected InventoryService $inventory;
    protected PdfService $pdf;
    protected LogService $log;
    protected NotificationService $notify;

    public function __construct(InventoryService $inventory, PdfService $pdf, LogService $log, NotificationService $notify)
    {
        $this->inventory = $inventory;
        $this->pdf = $pdf;
        $this->log = $log;
        $this->notify = $notify;
    }

    public function createPemesanan(array $data)
    {
        // 1. periksa stok semua item
        $items = $data['items'];
        $insufficient = [];
        foreach ($items as $it) {
            if (!empty($it['id_item'])) {
                if (!$this->inventory->hasStock($it['id_item'], $it['volume'])) {
                    $insufficient[] = $it;
                }
            }
        }
        if (!empty($insufficient)) {
            return ['error' => 'stok_tidak_cukup', 'details' => $insufficient];
        }

        // 2. buat pemesanan (approved otomatis)
        DB::beginTransaction();
        try {
            $p = Pemesanan::create([
                'sso_user_id' => $data['sso_user_id'] ?? null,
                'asal_instalasi' => $data['asal_instalasi'] ?? null,
                'status' => 'approved'
            ]);

            foreach ($items as $it) {
                DetailPemesanan::create([
                    'id_pemesanan' => $p->id_pemesanan,
                    'id_satuan' => $it['id_satuan'],
                    'volume' => $it['volume']
                ]);
            }

            $this->log->record('create_pemesanan','pemesanan', "Pemesanan {$p->id_pemesanan} dibuat");
            DB::commit();

            // generate struk (opsional) -> gunakan pdf service
            // $path = $this->pdf->generate('pemesanan.struk', ['pemesanan'=>$p->load('details')], "pemesanan/pemesanan_{$p->id_pemesanan}.pdf");

            return $p->load('details.satuan');
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getPemesanan($id)
    {
        return Pemesanan::with('details.satuan','user')->findOrFail($id);
    }

    public function downloadStruk($id)
    {
        $p = $this->getPemesanan($id);
        $path = "pemesanan/pemesanan_{$p->id_pemesanan}.pdf";
        $this->pdf->generate('pemesanan.struk', ['pemesanan'=>$p], $path);
        return ['file_url'=> asset("storage/{$path}")];
    }
}
