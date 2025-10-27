<?php
namespace App\Services;

use App\Models\Penerimaan;
use App\Models\DetailPenerimaan;
use App\Models\Bast;
use App\Models\detailBast;
use App\Services\InventoryService;
use App\Services\PdfService;
use App\Services\LogService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PenerimaanService
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

    public function createPenerimaan(array $data)
    {
        DB::beginTransaction();
        try {
            $p = Penerimaan::create([
                'sso_user_id' => $data['sso_user_id'] ?? null,
                'tanggal_penerimaan' => $data['tanggal_penerimaan'],
                'total_harga' => $data['total_harga'] ?? 0,
                'status' => 'pending'
            ]);

            foreach ($data['details'] as $d) {
                DetailPenerimaan::create([
                    'id_penerimaan' => $p->id_penerimaan,
                    'id_item' => $d['id_item'],
                    'id_category' => $d['id_category'],
                    'volume' => $d['volume'],
                    'id_satuan' => $d['id_satuan'],
                    'harga' => $d['harga'],
                    'is_layak' => $d['is_layak'] ?? true
                ]);
            }

            $this->log->record('create_penerimaan','penerimaan',"Penerimaan {$p->id_penerimaan} dibuat");
            DB::commit();
            return $p->load('details.item','details.satuan');
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function setDetailLayak($idDetail, bool $isLayak)
    {
        $detail = DetailPenerimaan::findOrFail($idDetail);
        $detail->is_layak = $isLayak;
        $detail->save();

        $penerimaan = $detail->penerimaan()->with('details')->first();
        $allLayak = $penerimaan->details->every(fn($d) => $d->is_layak);

        if ($allLayak && $penerimaan->status !== 'approved') {
            DB::beginTransaction();
            try {
                // update status
                $penerimaan->status = 'approved';
                $penerimaan->save();

                // update stok tiap item
                foreach ($penerimaan->details as $d) {
                    $this->inventory->incrementStock($d->id_item, (float)$d->volume);
                }

                // generate BAST otomatis
                $noSurat = 'BAST-'.date('Ymd').'-'.Str::random(5);
                $path = "bast/bast_{$noSurat}_{$penerimaan->id_penerimaan}.pdf";
                $pdfData = ['no_surat'=>$noSurat, 'penerimaan'=>$penerimaan, 'deskripsi'=>"BAST otomatis untuk penerimaan {$penerimaan->id_penerimaan}", 'staker'=>[]];
                $this->pdf->generate('bast.pdf', $pdfData, $path);

                $bast = Bast::create([
                    'no_surat' => $noSurat,
                    'id_penerimaan' => $penerimaan->id_penerimaan,
                    'deskripsi' => $pdfData['deskripsi'],
                    'file_path' => $path
                ]);

                // buat detail_bast
                foreach ($penerimaan->details as $d) {
                    if (class_exists(detailBast::class)) {
                        detailBast::create([
                            'id_bast' => $bast->id_bast,
                            'id_item' => $d->id_item,
                            'id_satuan' => $d->id_satuan,
                            'volume' => $d->volume,
                            'keterangan' => null
                        ]);
                    }
                }

                $this->log->record('approve_penerimaan','penerimaan',"Penerimaan {$penerimaan->id_penerimaan} disetujui, BAST {$bast->id_bast} dibuat");
                $this->notify->send($penerimaan->sso_user_id ?? 0, 'Penerimaan Disetujui', "Penerimaan #{$penerimaan->id_penerimaan} telah disetujui", "/penerimaan/{$penerimaan->id_penerimaan}");

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                throw $e;
            }
        }

        return ['ok'=>true,'auto_approved'=>$allLayak];
    }

    public function getPenerimaan($id)
    {
        return Penerimaan::with('details.item','details.satuan','bast')->findOrFail($id);
    }

    public function getBast($id)
    {
        // cari bast berdasarkan id_bast atau id_penerimaan (disambiguate)
        $b = Bast::find($id);
        if ($b) return $b;
        return Bast::where('id_penerimaan', $id)->first();
    }

    // method createBastManual etc...
    public function createBastManual(array $data)
    {
        // implement serupa generate otomatis, tetapi gunakan no_surat dari $data
        // ...
    }
}
