<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClothingTypeRequest;
use App\Http\Resources\ClothingTypeResource;
use App\Services\ClothingTypeService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClothingTypeController extends Controller
{
    private ClothingTypeService $clothingTypeService;

    public function __construct(ClothingTypeService $clothingTypeService)
    {
        $this->clothingTypeService = $clothingTypeService;
    }

    public function index(Request $request): JsonResponse
    {
        $fields = ['ulid', 'code', 'type', 'category', 'base_price'];

        $perPage = (int) $request->query('per_page', 10);
        $search = $request->query('search');

        $clothingTypes = $this->clothingTypeService->getAll($perPage, $search, $fields);

        return response()->json($clothingTypes, 200);
    }

    public function show(string $ulid): JsonResponse
    {
        try {
            $fields = ['ulid', 'code', 'type', 'category', 'base_price'];

            $clothingType = $this->clothingTypeService->getByUlid($ulid, $fields);

            return response()->json(new ClothingTypeResource(['clothing_type' => $clothingType]), 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Data jenis pakaian tidak ditemukan'
            ], 404);
        }
    }

    public function store(ClothingTypeRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $clothingType = $this->clothingTypeService->create($validatedData);

        return response()->json(new ClothingTypeResource([
            'message' => 'Pendaftaran jenis pakaian baru berhasil',
            'clothing_type' => $clothingType
        ]), 201);
    }

    public function update(ClothingTypeRequest $request, string $ulid): JsonResponse
    {
        try {
            $validatedData = $request->validated();

            $clothingType = $this->clothingTypeService->update($ulid, $validatedData);

            return response()->json(new ClothingTypeResource($clothingType), 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Data jenis pakaian tidak ditemukan'
            ], 404);
        }
    }

    public function destroy(string $ulid): JsonResponse
    {
        try {
            $this->clothingTypeService->delete($ulid);

            return response()->json([
                'message' => 'Hapus data jenis pakaian berhasil..!!'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Data jenis pakaian tidak ditemukan'
            ], 404);
        }
    }
}