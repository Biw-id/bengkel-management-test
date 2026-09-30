<?php

namespace App\Http\Controllers;

use App\Models\FormServiceAdvisor;
use App\Models\MasterMekanik;
use App\Models\MasterPart;
use App\Models\MasterJasa;
use App\Models\WorkOrder;
use App\Models\DetailWorkOrder;
use App\Services\IdGeneratorService;
use App\Services\WorkOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class WorkOrderController extends Controller
{
    protected IdGeneratorService $idGenerator;
    protected WorkOrderService $woService;

    public function __construct(IdGeneratorService $idGenerator, WorkOrderService $woService)
    {
        $this->idGenerator = $idGenerator;
        $this->woService = $woService;
    }

    public function index()
    {
        // Menampilkan Form SA (karena tidak ada FK ke WO, tampilkan semua)
        $formSaList = FormServiceAdvisor::orderBy('created_at', 'desc')->get();
        $workOrders = WorkOrder::with('mekanik')->orderBy('created_at', 'desc')->get();
        return view('work-order.index', compact('formSaList', 'workOrders'));
    }

    public function create($id_form_sa)
    {
        $formSa = FormServiceAdvisor::with('customer')->findOrFail($id_form_sa);
        $mekaniks = MasterMekanik::orderBy('nama_mekanik')->get();
        $jasas = MasterJasa::orderBy('keterangan')->get();
        $parts = MasterPart::orderBy('nama_part')->get();

        return view('work-order.create', compact('formSa', 'mekaniks', 'jasas', 'parts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_form_sa' => 'required|exists:form_service_advisor,id_form_sa',
            'id_mekanik' => 'required|exists:master_mekanik,honda_id_mekanik',
        ]);

        // Cek Double Work Order
        $existingWo = WorkOrder::where('id_form_sa', $request->id_form_sa)->first();
        if ($existingWo) {
            return response()->json([
                'success' => false,
                'message' => 'Form SA ini sudah memiliki Work Order.'
            ], 400); // Bad Request
        }

        $id_wo = $this->idGenerator->generateIdWorkOrder();

        $wo = WorkOrder::create([
            'id_wo' => $id_wo,
            'id_form_sa' => $request->id_form_sa,
            'waktu_wo' => now(),
            'status_wo' => 'PROSES',
            'id_mekanik' => $request->id_mekanik,
            'total_bayar_jasa' => 0,
            'total_bayar_part' => 0,
            'total_bayar_keseluruhan' => 0,
        ]);

        // Kita asumsikan saat store dari form SA, kita langsung arahkan ke halaman edit cart (create view yang tadi dipakai tapi sekarang mode edit)
        return redirect()->route('work-orders.edit', $wo->id_wo)->with('success', 'Work Order berhasil dibuat.');
    }

    public function edit($id_wo)
    {
        $wo = WorkOrder::with(['mekanik', 'detailWorkOrders', 'formServiceAdvisor.customer'])->findOrFail($id_wo);
        $jasas = MasterJasa::orderBy('keterangan')->get();
        $parts = MasterPart::orderBy('nama_part')->get();

        return view('work-order.edit', compact('wo', 'jasas', 'parts'));
    }

    // --- API Endpoints ---

    public function addPart(Request $request, $id_wo)
    {
        $request->validate([
            'part_number' => 'required|exists:master_part,part_number',
            'qty' => 'required|integer|min:1',
        ]);

        try {
            $detail = $this->woService->addPartToWorkOrder($id_wo, $request->part_number, $request->qty);
            $wo = WorkOrder::findOrFail($id_wo);
            
            return response()->json([
                'success' => true,
                'message' => 'Part berhasil ditambahkan.',
                'data' => [
                    'detail' => $detail,
                    'total_bayar_part' => $wo->total_bayar_part,
                    'total_bayar_keseluruhan' => $wo->total_bayar_keseluruhan
                ]
            ]);
        } catch (Exception $e) {
            if ($e->getMessage() === 'INSUFFICIENT_STOCK') {
                return response()->json([
                    'success' => false,
                    'code' => 'INSUFFICIENT_STOCK',
                    'message' => 'Stok RFS tidak cukup'
                ], 400);
            }
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem'
            ], 500);
        }
    }

    public function removePart($id_detail)
    {
        try {
            $detail = DetailWorkOrder::findOrFail($id_detail);
            $id_wo = $detail->id_wo;
            $this->woService->removePartFromWorkOrder($id_detail);
            $wo = WorkOrder::findOrFail($id_wo);

            return response()->json([
                'success' => true,
                'message' => 'Part berhasil dihapus.',
                'data' => [
                    'total_bayar_part' => $wo->total_bayar_part,
                    'total_bayar_keseluruhan' => $wo->total_bayar_keseluruhan
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus Part'
            ], 500);
        }
    }

    public function addJasa(Request $request, $id_wo)
    {
        $request->validate([
            'id_job' => 'required|exists:master_jasa,id_job',
            'qty' => 'required|integer|min:1',
        ]);

        try {
            $detail = $this->woService->addJasaToWorkOrder($id_wo, $request->id_job, $request->qty);
            $wo = WorkOrder::findOrFail($id_wo);

            return response()->json([
                'success' => true,
                'message' => 'Jasa berhasil ditambahkan.',
                'data' => [
                    'detail' => $detail,
                    'total_bayar_jasa' => $wo->total_bayar_jasa,
                    'total_bayar_keseluruhan' => $wo->total_bayar_keseluruhan
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem'
            ], 500);
        }
    }

    public function removeJasa($id_detail)
    {
        try {
            $detail = DetailWorkOrder::findOrFail($id_detail);
            $id_wo = $detail->id_wo;
            $this->woService->removeJasaFromWorkOrder($id_detail);
            $wo = WorkOrder::findOrFail($id_wo);

            return response()->json([
                'success' => true,
                'message' => 'Jasa berhasil dihapus.',
                'data' => [
                    'total_bayar_jasa' => $wo->total_bayar_jasa,
                    'total_bayar_keseluruhan' => $wo->total_bayar_keseluruhan
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus Jasa'
            ], 500);
        }
    }

    public function complete(Request $request, $id_wo)
    {
        $request->validate([
            'tipe_unit' => 'required|string|max:100',
        ]);

        try {
            // Ambil WO beserta relasinya untuk mendapatkan id_customer
            $wo = WorkOrder::with('formServiceAdvisor')->findOrFail($id_wo);
            
            if (!$wo->formServiceAdvisor) {
                 throw new Exception("Work Order tidak memiliki relasi ke Form SA. Tidak dapat menyelesaikan.");
            }

            $id_customer = $wo->formServiceAdvisor->id_customer;

            $result = $this->woService->completeWorkOrder($id_wo, $id_customer, $request->tipe_unit);

            return response()->json([
                'success' => true,
                'api_success' => $result['api_success'],
                'message' => $result['message'],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
