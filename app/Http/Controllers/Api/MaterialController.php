<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MaterialRequest;
use App\Http\Resources\MaterialResource;
use App\Services\MaterialService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    private MaterialService $materialService;

    public function __construct(MaterialService $materialService)
    {
        $this->materialService = $materialService;
    }

    public function index(Request $request): JsonResponse
    {
        $fields = ['ulid', 'code', 'name', 'description', 'photo', 'unit', 'stock', 'initial_stock'];

        $perPage = (int) $request->query('per_page', 10);
        $search = $request->query('search');

        $materials = $this->materialService->getAll($perPage, $search, $fields);

        return response()->json($materials, 200);
    }

    public function show(string $ulid): JsonResponse
    {
        try {
            $fields = ['ulid', 'code', 'name', 'description', 'photo', 'unit', 'stock', 'initial_stock'];

            $material = $this->materialService->getByUlid($ulid, $fields);

            return response()->json(new MaterialResource(['material' => $material]), 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Data bahan tidak ditemukan'
            ], 404);
        }
    }

    public function store(MaterialRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $material = $this->materialService->create($validatedData);

        return response()->json(new MaterialResource([
            'message' => 'Pendaftaran bahan baru berhasil',
            'material' => $material
        ]), 201);
    }

    public function update(MaterialRequest $request, string $ulid): JsonResponse
    {
        try {
            $validatedData = $request->validated();

            $material = $this->materialService->update($ulid, $validatedData);

            return response()->json(new MaterialResource($material), 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Data bahan tidak ditemukan'
            ], 404);
        }
    }

    public function destroy(string $ulid): JsonResponse
    {
        try {
            $this->materialService->delete($ulid);

            return response()->json([
                'message' => 'Hapus data bahan berhasil..!!'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Data bahan tidak ditemukan'
            ], 404);
        }
    }
}