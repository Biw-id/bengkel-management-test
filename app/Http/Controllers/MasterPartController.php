<?php

namespace App\Http\Controllers;

use App\Http\Requests\MasterPartRequest;
use App\Services\MasterDataService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MasterPartController extends Controller
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

        return view('master.part.index');
    }

    public function data(Request $request): JsonResponse
    {
        $search = $request->get('search');
        $limit = (int) ($request->get('limit', 10));
        $paginated = $this->service->listPart($search, $limit);

        return response()->json([
            'success' => true,
            'message' => 'Data Master Part berhasil diambil',
            'data' => [
                'items' => $paginated->items(),
                'total' => $paginated->total(),
                'page' => $paginated->currentPage(),
                'limit' => $paginated->perPage(),
                'totalPages' => $paginated->lastPage(),
            ],
        ]);
    }

    public function store(MasterPartRequest $request): JsonResponse
    {
        $part = $this->service->createPart($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Data Master Part berhasil ditambahkan',
            'data' => $part,
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $part = $this->service->getPartById($id);

        return response()->json([
            'success' => true,
            'message' => 'Detail Master Part',
            'data' => $part,
        ]);
    }

    public function update(MasterPartRequest $request, string $id): JsonResponse
    {
        $part = $this->service->updatePart($id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Data Master Part berhasil diperbarui',
            'data' => $part,
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $this->service->deletePart($id);

            return response()->json([
                'success' => true,
                'message' => 'Data Master Part berhasil dihapus',
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
