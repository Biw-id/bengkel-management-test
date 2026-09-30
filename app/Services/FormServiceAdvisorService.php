<?php

namespace App\Services;

use App\Models\FormServiceAdvisor;
use App\Models\MasterCustomer;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class FormServiceAdvisorService
{
    protected IdGeneratorService $idGenerator;

    public function __construct(IdGeneratorService $idGenerator)
    {
        $this->idGenerator = $idGenerator;
    }

    public function listFormSa(?string $search = null, int $limit = 10): LengthAwarePaginator
    {
        $limit = min(max($limit, 1), 100);
        $query = FormServiceAdvisor::with('customer');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id_form_sa', 'like', "%{$search}%")
                  ->orWhere('id_customer', 'like', "%{$search}%")
                  ->orWhere('no_plat', 'like', "%{$search}%")
                  ->orWhere('keluhan_konsumen', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('nama_customer', 'like', "%{$search}%");
                  });
            });
        }

        return $query->orderBy('waktu_form_sa', 'desc')->paginate($limit);
    }

    public function createFormSa(array $data): FormServiceAdvisor
    {
        $idFormSa = $this->idGenerator->generateIdFormSa();

        return FormServiceAdvisor::create([
            'id_form_sa' => $idFormSa,
            'id_customer' => $data['id_customer'],
            'waktu_form_sa' => Carbon::now(),
            'keluhan_konsumen' => $data['keluhan_konsumen'],
            'no_plat' => $data['no_plat'],
        ]);
    }

    public function getFormSaById(string $idFormSa): FormServiceAdvisor
    {
        return FormServiceAdvisor::with('customer')->findOrFail($idFormSa);
    }

    public function getNextIdPreview(): string
    {
        return $this->idGenerator->generateIdFormSa();
    }
}
