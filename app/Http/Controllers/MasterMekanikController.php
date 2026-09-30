<?php

namespace App\Http\Controllers;

use App\Http\Requests\MasterMekanikRequest;
use App\Services\MasterDataService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MasterMekanikController extends Controller
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

        return view('master.mekanik.index');
    }

    public function data(Request $request): JsonResponse
    {
        $search = $request->get('search');
        $limit = (int) ($request->get('limit', 10));
        $paginated = $this->service->listMekanik($search, $limit);

        return response()->json([
            'success' => true,
            'message' => 'Data Master Mekanik berhasil diambil',
            'data' => [
                'items' => $paginated->items(),
                'total' => $paginated->total(),
                'page' => $paginated->currentPage(),
                'limit' => $paginated->perPage(),
                'totalPages' => $paginated->lastPage(),
            ],
        ]);
    }

    public function store(MasterMekanikRequest $request): JsonResponse
    {
        $mekanik = $this->service->createMekanik($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Data Master Mekanik berhasil ditambahkan',
            'data' => $mekanik,
        ], 201);
    }

    public function show($id): JsonResponse
    {
        $mekanik = $this->service->getMekanikById($id);

        return response()->json([
            'success' => true,
            'message' => 'Detail Master Mekanik',
            'data' => $mekanik,
        ]);
    }

    public function update(MasterMekanikRequest $request, $id): JsonResponse
    {
        $mekanik = $this->service->updateMekanik($id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Data Master Mekanik berhasil diperbarui',
            'data' => $mekanik,
        ]);
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->service->deleteMekanik($id);

            return response()->json([
                'success' => true,
                'message' => 'Data Master Mekanik berhasil dihapus',
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
