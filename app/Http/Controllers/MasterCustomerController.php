<?php

namespace App\Http\Controllers;

use App\Http\Requests\MasterCustomerRequest;
use App\Services\MasterDataService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MasterCustomerController extends Controller
{
    protected MasterDataService $service;

    public function __construct(MasterDataService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax() || $request->wantsJson()) {
            return $this->data($request);
        }

        return view('master.customer.index');
    }

    public function data(Request $request): JsonResponse
    {
        $search = $request->get('search');
        $limit = (int) ($request->get('limit', 10));
        $paginated = $this->service->listCustomer($search, $limit);

        return response()->json([
            'success' => true,
            'message' => 'Data Master Customer berhasil diambil',
            'data' => [
                'items' => $paginated->items(),
                'total' => $paginated->total(),
                'page' => $paginated->currentPage(),
                'limit' => $paginated->perPage(),
                'totalPages' => $paginated->lastPage(),
            ],
        ]);
    }

    public function store(MasterCustomerRequest $request): JsonResponse
    {
        $customer = $this->service->createCustomer($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Data Master Customer berhasil ditambahkan',
            'data' => $customer,
        ], 201);
    }

    public function show($id): JsonResponse
    {
        $customer = $this->service->getCustomerById($id);

        return response()->json([
            'success' => true,
            'message' => 'Detail Master Customer',
            'data' => $customer,
        ]);
    }

    public function update(MasterCustomerRequest $request, $id): JsonResponse
    {
        $customer = $this->service->updateCustomer($id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Data Master Customer berhasil diperbarui',
            'data' => $customer,
        ]);
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->service->deleteCustomer($id);

            return response()->json([
                'success' => true,
                'message' => 'Data Master Customer berhasil dihapus',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'code' => 'REF_IN_USE',
                'message' => $e->getMessage(),
            ], 409);
        }
    }
}
