<?php

namespace App\Services;

use App\Models\DetailWorkOrder;
use App\Models\MasterPart;
use App\Models\WorkOrder;
use App\Models\NotaJasaBengkel;
use App\Models\NotaSukuCadangSo;
use App\Models\NotaSukuCadangH3;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class WorkOrderService
{
    protected IdGeneratorService $idGenerator;
    protected MainDealerApiService $apiService;

    public function __construct(IdGeneratorService $idGenerator, MainDealerApiService $apiService)
    {
        $this->idGenerator = $idGenerator;
        $this->apiService = $apiService;
    }
    /**
     * Menambahkan Part ke Work Order dengan aturan stok mutlak (RFS ke Booked)
     */
    public function addPartToWorkOrder(string $id_wo, string $part_number, int $qty): DetailWorkOrder
    {
        return DB::transaction(function () use ($id_wo, $part_number, $qty) {
            $part = MasterPart::where('part_number', $part_number)->lockForUpdate()->firstOrFail();

            if ($qty > $part->qty_rfs) {
                throw new Exception("INSUFFICIENT_STOCK");
            }

            // Kurangi RFS, tambah Book
            $part->qty_rfs -= $qty;
            $part->qty_book += $qty;
            $part->save();

            $detail = DetailWorkOrder::create([
                'id_wo' => $id_wo,
                'jenis_penanganan' => 'Part',
                'biaya' => $part->harga,
                'deskripsi' => $part->nama_part . ' (' . $part_number . ')',
                'qty' => $qty,
            ]);

            $this->updateWorkOrderTotals($id_wo);

            return $detail;
        });
    }

    /**
     * Menghapus Part dari Work Order (kembalikan stok)
     */
    public function removePartFromWorkOrder(int $id_detail): void
    {
        DB::transaction(function () use ($id_detail) {
            $detail = DetailWorkOrder::where('id_detail', $id_detail)->where('jenis_penanganan', 'Part')->lockForUpdate()->firstOrFail();
            $id_wo = $detail->id_wo;

            $part_number = $this->extractPartNumber($detail->deskripsi);
            $part = MasterPart::where('part_number', $part_number)->lockForUpdate()->first();
            if ($part) {
                $part->qty_rfs += $detail->qty;
                $part->qty_book -= $detail->qty;
                $part->save();
            }

            $detail->delete();
            $this->updateWorkOrderTotals($id_wo);
        });
    }

    /**
     * Menambahkan Jasa ke Work Order
     */
    public function addJasaToWorkOrder(string $id_wo, string $id_job, int $qty): DetailWorkOrder
    {
        return DB::transaction(function () use ($id_wo, $id_job, $qty) {
            $jasa = \App\Models\MasterJasa::where('id_job', $id_job)->firstOrFail();

            $detail = DetailWorkOrder::create([
                'id_wo' => $id_wo,
                'jenis_penanganan' => 'Jasa',
                'biaya' => $jasa->harga,
                'deskripsi' => $jasa->keterangan . ' (' . $id_job . ')',
                'qty' => $qty,
            ]);

            $this->updateWorkOrderTotals($id_wo);

            return $detail;
        });
    }

    /**
     * Menghapus Jasa dari Work Order
     */
    public function removeJasaFromWorkOrder(int $id_detail): void
    {
        DB::transaction(function () use ($id_detail) {
            $detail = DetailWorkOrder::where('id_detail', $id_detail)->where('jenis_penanganan', 'Jasa')->firstOrFail();
            $id_wo = $detail->id_wo;
            
            $detail->delete();
            $this->updateWorkOrderTotals($id_wo);
        });
    }

    /**
     * Update total bayar pada Work Order
     */
    public function updateWorkOrderTotals(string $id_wo): void
    {
        $wo = WorkOrder::where('id_wo', $id_wo)->firstOrFail();
        
        $totalJasa = DetailWorkOrder::where('id_wo', $id_wo)
            ->where('jenis_penanganan', 'Jasa')
            ->sum(DB::raw('biaya * qty'));
            
        $totalPart = DetailWorkOrder::where('id_wo', $id_wo)
            ->where('jenis_penanganan', 'Part')
            ->sum(DB::raw('biaya * qty'));
            
        $wo->total_bayar_jasa = $totalJasa;
        $wo->total_bayar_part = $totalPart;
        $wo->total_bayar_keseluruhan = $totalJasa + $totalPart;
        $wo->save();
    }

    /**
     * Ekstrak Part Number yang valid dari deskripsi
     */
    public function extractPartNumber(string $deskripsi): string
    {
        // Prioritas 1: Ambil dari kurung paling belakang: e.g. "Kampas Rem (Brake Shoe) (43130KZL930)" -> 43130KZL930
        if (preg_match('/.*\(([^()]+)\)\s*$/', $deskripsi, $matches)) {
            $candidate = trim($matches[1]);
            if (MasterPart::where('part_number', $candidate)->exists()) {
                return $candidate;
            }
        }

        // Prioritas 2: Loop semua kurung dari belakang
        if (preg_match_all('/\(([^()]+)\)/', $deskripsi, $allMatches)) {
            foreach (array_reverse($allMatches[1]) as $match) {
                $candidate = trim($match);
                if (MasterPart::where('part_number', $candidate)->exists()) {
                    return $candidate;
                }
            }
        }

        // Prioritas 3: Cek apakah mengandung part_number yang ada di DB
        $parts = MasterPart::select('part_number')->get();
        foreach ($parts as $p) {
            if (str_contains($deskripsi, $p->part_number)) {
                return $p->part_number;
            }
        }

        return $deskripsi;
    }

    /**
     * Ekstrak ID Job yang valid dari deskripsi
     */
    public function extractJobId(string $deskripsi): string
    {
        if (preg_match('/.*\(([^()]+)\)\s*$/', $deskripsi, $matches)) {
            $candidate = trim($matches[1]);
            if (\App\Models\MasterJasa::where('id_job', $candidate)->exists()) {
                return $candidate;
            }
        }

        if (preg_match_all('/\(([^()]+)\)/', $deskripsi, $allMatches)) {
            foreach (array_reverse($allMatches[1]) as $match) {
                $candidate = trim($match);
                if (\App\Models\MasterJasa::where('id_job', $candidate)->exists()) {
                    return $candidate;
                }
            }
        }

        $jasas = \App\Models\MasterJasa::select('id_job')->get();
        foreach ($jasas as $j) {
            if (str_contains($deskripsi, $j->id_job)) {
                return $j->id_job;
            }
        }

        return $deskripsi;
    }

    /**
     * Menyelesaikan Work Order dan mengirim data ke API Main Dealer
     */
    public function completeWorkOrder(string $id_wo, string $id_customer, string $tipe_unit): array
    {
        $wo = WorkOrder::with(['detailWorkOrders', 'mekanik'])->where('id_wo', $id_wo)->firstOrFail();

        if ($wo->status_wo === 'SELESAI') {
            throw new Exception("Work Order sudah diselesaikan sebelumnya.");
        }

        $payload = [];
        $apiSuccess = true;

        DB::transaction(function () use ($wo, $id_customer, $tipe_unit, &$payload) {
            $now = now();
            
            // 1. Ubah status WO
            $wo->status_wo = 'SELESAI';
            $wo->save();

            // 2. Generate NJB
            $id_njb = $this->idGenerator->generateIdNjb($now);
            NotaJasaBengkel::create([
                'id_njb' => $id_njb,
                'id_wo' => $wo->id_wo,
                'waktu_njb' => $now,
                'id_mekanik' => $wo->id_mekanik,
                'tipe_unit' => $tipe_unit,
            ]);

            // 3. Generate NSC
            $no_nsc = $this->idGenerator->generateIdNsc($now);
            NotaSukuCadangSo::create([
                'no_nsc' => $no_nsc,
                'tgl_nsc' => $now,
                'id_customer' => $id_customer,
                'amount' => $wo->total_bayar_part,
                'id_wo' => $wo->id_wo,
                'diskon_part' => 0,
                'total_amount' => $wo->total_bayar_part,
            ]);

            $njbPayload = [];
            $nscPayload = [];

            // 4. Proses Detail Jasa dan Part
            foreach ($wo->detailWorkOrders as $detail) {
                if ($detail->jenis_penanganan === 'Jasa') {
                    $idJob = $this->extractJobId($detail->deskripsi);

                    $njbPayload[] = [
                        'idJob' => $idJob,
                        'hargaServis' => $detail->biaya * $detail->qty,
                        'createdTime' => $now->format('Y-m-d H:i:s'),
                        'modifiedTime' => $now->format('Y-m-d H:i:s'),
                    ];
                } elseif ($detail->jenis_penanganan === 'Part') {
                    $partNumber = $this->extractPartNumber($detail->deskripsi);

                    NotaSukuCadangH3::create([
                        'no_nsc' => $no_nsc,
                        'part_number' => $partNumber,
                        'qty' => $detail->qty,
                        'harga' => $detail->biaya,
                        'jumlah_bayar' => $detail->biaya * $detail->qty,
                        'id_wo' => $wo->id_wo,
                    ]);

                    // Kurangi qty_book karena sudah valid terjual
                    $part = MasterPart::where('part_number', $partNumber)->lockForUpdate()->first();
                    if ($part) {
                        $part->qty_book -= $detail->qty;
                        $part->save();
                    }

                    $nscPayload[] = [
                        'idJob' => $wo->id_wo,
                        'partsNumber' => $partNumber,
                        'kuantitas' => $detail->qty,
                        'hargaParts' => $detail->biaya,
                        'totalHargaParts' => $detail->biaya * $detail->qty,
                        'createdTime' => $now->format('Y-m-d H:i:s'),
                        'modifiedTime' => $now->format('Y-m-d H:i:s'),
                    ];
                }
            }

            // 5. Susun Payload API
            $payload = [
                'noWorkOrder' => $wo->id_wo,
                'idCustomer' => $id_customer,
                'hondaIdMekanik' => $wo->mekanik->honda_id_mekanik,
                'noNJB' => $id_njb,
                'tanggalNJB' => $now->format('Y-m-d H:i:s'),
                'totalHargaNJB' => $wo->total_bayar_jasa,
                'noNSC' => $no_nsc,
                'tanggalNSC' => $now->format('Y-m-d H:i:s'),
                'totalHargaNSC' => $wo->total_bayar_part,
                'dealerId' => IdGeneratorService::DEALER_ID,
                'createdTime' => $wo->created_at->format('Y-m-d H:i:s'),
                'modifiedTime' => $now->format('Y-m-d H:i:s'),
                'njb' => $njbPayload,
                'nsc' => $nscPayload,
            ];
        });

        // 6. Integrasi API (Diluar transaksi DB agar kegagalan API tidak me-rollback status Selesai)
        try {
            $this->apiService->submitTransaction($payload);
        } catch (Exception $e) {
            if ($e->getMessage() === 'API_CONNECTION_FAILED') {
                $apiSuccess = false;
            } else {
                // Unexpected error, still treat as API failure for UI purpose
                Log::error('Unexpected API Error: ' . $e->getMessage());
                $apiSuccess = false;
            }
        }

        return [
            'success' => true,
            'api_success' => $apiSuccess,
            'message' => $apiSuccess ? 'Work Order selesai dan data terkirim ke Pusat.' : 'WO Selesai, tapi pengiriman data ke Pusat tertunda (API Down).'
        ];
    }
}
