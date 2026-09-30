<?php

namespace App\Http\Controllers;

use App\Models\NotaJasaBengkel;
use App\Models\NotaSukuCadangSo;
use Illuminate\Http\Request;

class NotaController extends Controller
{
    public function index()
    {
        $njbList = NotaJasaBengkel::with([
            'workOrder.mekanik',
            'workOrder.formServiceAdvisor.customer',
            'workOrder.detailWorkOrders' => function ($q) {
                $q->where('jenis_penanganan', 'Jasa');
            }
        ])->orderBy('created_at', 'desc')->get();

        $nscList = NotaSukuCadangSo::with([
            'customer',
            'workOrder.mekanik',
            'workOrder.formServiceAdvisor',
            'notaSukuCadangH3.part'
        ])->orderBy('created_at', 'desc')->get();

        return view('nota.index', compact('njbList', 'nscList'));
    }
}
