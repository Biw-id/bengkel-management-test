<?php

namespace App\Services;

use App\Models\DetailWorkOrder;
use App\Models\FormServiceAdvisor;
use App\Models\MasterCustomer;
use App\Models\MasterJasa;
use App\Models\MasterMekanik;
use App\Models\MasterPart;
use App\Models\NotaJasaBengkel;
use App\Models\NotaSukuCadangH3;
use App\Models\NotaSukuCadangSo;
use App\Models\WorkOrder;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;

class MasterDataService
{
    // ==========================================
    // MASTER JASA
    // ==========================================

    public function listJasa(?string $search = null, int $limit = 20): LengthAwarePaginator
    {
        $limit = min(max($limit, 1), 100);
        $query = MasterJasa::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id_job', 'like', "%{$search}%")
                  ->orWhere('kode_motor', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($limit);
    }

    public function createJasa(array $data): MasterJasa
    {
        return MasterJasa::create($data);
    }

    public function getJasaById(string $idJob): MasterJasa
    {
        return MasterJasa::findOrFail($idJob);
    }

    public function updateJasa(string $idJob, array $data): MasterJasa
    {
        $jasa = MasterJasa::findOrFail($idJob);
        $jasa->update($data);
        return $jasa;
    }

    public function deleteJasa(string $idJob): bool
    {
        $jasa = MasterJasa::findOrFail($idJob);

        // Cek apakah jasa digunakan di Detail Work Order aktif
        $inUse = DetailWorkOrder::where('jenis_penanganan', 'Jasa')
            ->where('deskripsi', 'like', "%{$idJob}%")
            ->exists();

        if ($inUse) {
            throw new Exception("Master Jasa {$idJob} sedang digunakan dalam riwayat servis aktif dan tidak dapat dihapus.");
        }

        return $jasa->delete();
    }

    // ==========================================
    // MASTER PART
    // ==========================================

    public function listPart(?string $search = null, int $limit = 20): LengthAwarePaginator
    {
        $limit = min(max($limit, 1), 100);
        $query = MasterPart::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('part_number', 'like', "%{$search}%")
                  ->orWhere('nama_part', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($limit);
    }

    public function createPart(array $data): MasterPart
    {
        return MasterPart::create($data);
    }

    public function getPartById(string $partNumber): MasterPart
    {
        return MasterPart::findOrFail($partNumber);
    }

    public function updatePart(string $partNumber, array $data): MasterPart
    {
        $part = MasterPart::findOrFail($partNumber);
        $part->update($data);
        return $part;
    }

    public function deletePart(string $partNumber): bool
    {
        $part = MasterPart::findOrFail($partNumber);

        // Cek apakah part sedang dipakai di Nota Suku Cadang H3
        $inUse = NotaSukuCadangH3::where('part_number', $partNumber)->exists();
        if ($inUse) {
            throw new Exception("Master Part {$partNumber} sedang digunakan dalam riwayat transaksi / nota dan tidak dapat dihapus.");
        }

        return $part->delete();
    }

    // ==========================================
    // MASTER MEKANIK
    // ==========================================

    public function listMekanik(?string $search = null, int $limit = 20): LengthAwarePaginator
    {
        $limit = min(max($limit, 1), 100);
        $query = MasterMekanik::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('honda_id_mekanik', 'like', "%{$search}%")
                  ->orWhere('nama_mekanik', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($limit);
    }

    public function createMekanik(array $data): MasterMekanik
    {
        return MasterMekanik::create($data);
    }

    public function getMekanikById($id): MasterMekanik
    {
        return MasterMekanik::findOrFail($id);
    }

    public function updateMekanik($id, array $data): MasterMekanik
    {
        $mekanik = MasterMekanik::findOrFail($id);
        $mekanik->update($data);
        return $mekanik;
    }

    public function deleteMekanik($id): bool
    {
        $mekanik = MasterMekanik::findOrFail($id);

        $inUse = WorkOrder::where('id_mekanik', $mekanik->honda_id_mekanik)->exists() ||
                 NotaJasaBengkel::where('id_mekanik', $mekanik->honda_id_mekanik)->exists();

        if ($inUse) {
            throw new Exception("Mekanik {$mekanik->nama_mekanik} ({$mekanik->honda_id_mekanik}) sedang memiliki riwayat pekerjaan servis aktif dan tidak dapat dihapus.");
        }

        return $mekanik->delete();
    }

    // ==========================================
    // MASTER CUSTOMER
    // ==========================================

    public function listCustomer(?string $search = null, int $limit = 20): LengthAwarePaginator
    {
        $limit = min(max($limit, 1), 100);
        $query = MasterCustomer::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id_customer', 'like', "%{$search}%")
                  ->orWhere('nama_customer', 'like', "%{$search}%")
                  ->orWhere('no_plat', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($limit);
    }

    public function createCustomer(array $data): MasterCustomer
    {
        return MasterCustomer::create($data);
    }

    public function getCustomerById($id): MasterCustomer
    {
        return MasterCustomer::findOrFail($id);
    }

    public function updateCustomer($id, array $data): MasterCustomer
    {
        $customer = MasterCustomer::findOrFail($id);
        $customer->update($data);
        return $customer;
    }

    public function deleteCustomer($id): bool
    {
        $customer = MasterCustomer::findOrFail($id);

        $inUse = FormServiceAdvisor::where('id_customer', $customer->id_customer)->exists() ||
                 NotaSukuCadangSo::where('id_customer', $customer->id_customer)->exists();

        if ($inUse) {
            throw new Exception("Konsumen {$customer->nama_customer} ({$customer->id_customer}) memiliki riwayat form SA / transaksi aktif dan tidak dapat dihapus.");
        }

        return $customer->delete();
    }
}
