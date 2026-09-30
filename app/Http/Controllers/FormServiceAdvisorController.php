<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFormServiceAdvisorRequest;
use App\Models\MasterCustomer;
use App\Services\FormServiceAdvisorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FormServiceAdvisorController extends Controller
{
    protected FormServiceAdvisorService $service;

    public function __construct(FormServiceAdvisorService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax() || $request->wantsJson()) {
            return $this->data($request);
        }

        $customers = MasterCustomer::orderBy('nama_customer', 'asc')->get();
        $nextIdPreview = $this->service->getNextIdPreview();

        return view('form-sa.index', compact('customers', 'nextIdPreview'));
    }

    public function data(Request $request): JsonResponse
    {
        $search = $request->get('search');
        $limit = (int) ($request->get('limit', 10));
        $paginated = $this->service->listFormSa($search, $limit);

        return response()->json([
            'success' => true,
            'message' => 'Data Form Service Advisor berhasil diambil',
            'data' => [
                'items' => $paginated->items(),
                'total' => $paginated->total(),
                'page' => $paginated->currentPage(),
                'limit' => $paginated->perPage(),
                'totalPages' => $paginated->lastPage(),
                'nextIdPreview' => $this->service->getNextIdPreview(),
            ],
        ]);
    }

    public function store(StoreFormServiceAdvisorRequest $request): JsonResponse
    {
        $formSa = $this->service->createFormSa($request->validated());

        return response()->json([
            'success' => true,
            'message' => "Form Service Advisor {$formSa->id_form_sa} berhasil dibuat",
            'data' => $formSa,
            'next_id' => $this->service->getNextIdPreview(),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $formSa = $this->service->getFormSaById($id);

        return response()->json([
            'success' => true,
            'message' => 'Detail Form Service Advisor',
            'data' => $formSa,
        ]);
    }
}
