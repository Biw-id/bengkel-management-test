<?php

namespace App\Services;

use App\Models\FormServiceAdvisor;
use App\Models\NotaJasaBengkel;
use App\Models\NotaSukuCadangSo;
use App\Models\WorkOrder;
use Carbon\Carbon;

class IdGeneratorService
{
    public const DEALER_ID = '36915';

    /**
     * Generate ID Form SA: SA/TAHUN/36915/0001 (e.g. SA/2026/36915/0001)
     */
    public function generateIdFormSa(?Carbon $date = null): string
    {
        $date = $date ?? Carbon::now();
        $year = $date->format('Y'); // 4-digit year

        $prefix = "SA/{$year}/" . self::DEALER_ID . "/";

        $latest = FormServiceAdvisor::where('id_form_sa', 'like', "{$prefix}%")
            ->orderBy('id_form_sa', 'desc')
            ->value('id_form_sa');

        $sequence = 1;
        if ($latest) {
            $lastSeqStr = substr($latest, strrpos($latest, '/') + 1);
            $sequence = ((int) $lastSeqStr) + 1;
        }

        return $prefix . sprintf('%04d', $sequence);
    }

    /**
     * Generate ID Work Order: WO/YY/MM/36915/00001 (e.g. WO/26/08/36915/00001)
     */
    public function generateIdWorkOrder(?Carbon $date = null): string
    {
        $date = $date ?? Carbon::now();
        $yy = $date->format('y'); // 2-digit year
        $mm = $date->format('m'); // 2-digit month

        $prefix = "WO/{$yy}/{$mm}/" . self::DEALER_ID . "/";

        $latest = WorkOrder::where('id_wo', 'like', "{$prefix}%")
            ->orderBy('id_wo', 'desc')
            ->value('id_wo');

        $sequence = 1;
        if ($latest) {
            $lastSeqStr = substr($latest, strrpos($latest, '/') + 1);
            $sequence = ((int) $lastSeqStr) + 1;
        }

        return $prefix . sprintf('%05d', $sequence);
    }

    /**
     * Generate ID Nota Jasa Bengkel: NJB/YY/MM/36915/00001 (e.g. NJB/26/08/36915/00001)
     */
    public function generateIdNjb(?Carbon $date = null): string
    {
        $date = $date ?? Carbon::now();
        $yy = $date->format('y'); // 2-digit year
        $mm = $date->format('m'); // 2-digit month

        $prefix = "NJB/{$yy}/{$mm}/" . self::DEALER_ID . "/";

        $latest = NotaJasaBengkel::where('id_njb', 'like', "{$prefix}%")
            ->orderBy('id_njb', 'desc')
            ->value('id_njb');

        $sequence = 1;
        if ($latest) {
            $lastSeqStr = substr($latest, strrpos($latest, '/') + 1);
            $sequence = ((int) $lastSeqStr) + 1;
        }

        return $prefix . sprintf('%05d', $sequence);
    }

    /**
     * Generate No Nota Suku Cadang SO: NSC/YY/MM/36915/00001 (e.g. NSC/26/08/36915/00001)
     */
    public function generateIdNsc(?Carbon $date = null): string
    {
        $date = $date ?? Carbon::now();
        $yy = $date->format('y'); // 2-digit year
        $mm = $date->format('m'); // 2-digit month

        $prefix = "NSC/{$yy}/{$mm}/" . self::DEALER_ID . "/";

        $latest = NotaSukuCadangSo::where('no_nsc', 'like', "{$prefix}%")
            ->orderBy('no_nsc', 'desc')
            ->value('no_nsc');

        $sequence = 1;
        if ($latest) {
            $lastSeqStr = substr($latest, strrpos($latest, '/') + 1);
            $sequence = ((int) $lastSeqStr) + 1;
        }

        return $prefix . sprintf('%05d', $sequence);
    }
}
