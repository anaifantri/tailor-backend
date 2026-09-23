<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductionProgressRequest;
use App\Http\Resources\ProductionProgressResource;
use App\Services\ProductionProgressService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class ProductionProgressController extends Controller
{
    protected ProductionProgressService $service;

    public function __construct(ProductionProgressService $service)
    {
        $this->service = $service;
    }

    public function show(string $ulid): JsonResponse
    {
        try {
            $progress = $this->service->getByUlid($ulid);

            return response()->json([
                'status' => 'success',
                'data' => new ProductionProgressResource($progress),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data progress pengerjaan tidak ditemukan.',
            ], 404);
        }
    }

    public function store(ProductionProgressRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $progress = $this->service->create($validatedData);

        return response()->json([
            'status' => 'success',
            'message' => 'Progress pengerjaan berhasil ditambahkan.',
            'data' => new ProductionProgressResource($progress),
        ], 201);
    }

    public function update(ProductionProgressRequest $request, string $ulid): JsonResponse
    {
        try {
            $validatedData = $request->validated();

            $progress = $this->service->update($ulid, $validatedData);

            return response()->json([
                'status' => 'success',
                'message' => 'Pembaruan progress pengerjaan berhasil.',
                'data' => new ProductionProgressResource($progress),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data progress pengerjaan tidak ditemukan.',
            ], 404);
        }
    }

    public function destroy(string $ulid): JsonResponse
    {
        try {
            $this->service->delete($ulid);

            return response()->json([
                'status' => 'success',
                'message' => 'Hapus data progress pengerjaan berhasil.',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data progress pengerjaan tidak ditemukan.',
            ], 404);
        }
    }
}