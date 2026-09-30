<?php

namespace App\Http\Controllers;

use App\Http\Requests\MasterJasaRequest;
use App\Services\MasterDataService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MasterJasaController extends Controller
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

        return view('master.jasa.index');
    }

    public function data(Request $request): JsonResponse
    {
        $search = $request->get('search');
        $limit = (int) ($request->get('limit', 10));
        $paginated = $this->service->listJasa($search, $limit);

        return response()->json([
            'success' => true,
            'message' => 'Data Master Jasa berhasil diambil',
            'data' => [
                'items' => $paginated->items(),
                'total' => $paginated->total(),
                'page' => $paginated->currentPage(),
                'limit' => $paginated->perPage(),
                'totalPages' => $paginated->lastPage(),
            ],
        ]);
    }

    public function store(MasterJasaRequest $request): JsonResponse
    {
        $jasa = $this->service->createJasa($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Data Master Jasa berhasil ditambahkan',
            'data' => $jasa,
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $jasa = $this->service->getJasaById($id);

        return response()->json([
            'success' => true,
            'message' => 'Detail Master Jasa',
            'data' => $jasa,
        ]);
    }

    public function update(MasterJasaRequest $request, string $id): JsonResponse
    {
        $jasa = $this->service->updateJasa($id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Data Master Jasa berhasil diperbarui',
            'data' => $jasa,
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $this->service->deleteJasa($id);

            return response()->json([
                'success' => true,
                'message' => 'Data Master Jasa berhasil dihapus',
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
