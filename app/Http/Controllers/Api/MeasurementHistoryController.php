<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MeasurementHistoryRequest;
use App\Http\Resources\MeasurementHistoryResource;
use App\Services\MeasurementHistoryService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeasurementHistoryController extends Controller
{
    protected MeasurementHistoryService $measurementHistoryService;

    public function __construct(MeasurementHistoryService $measurementHistoryService)
    {
        $this->measurementHistoryService = $measurementHistoryService;
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = $request->query('per_page', 10);
        $search = $request->query('search');

        $measurementHistories = $this->measurementHistoryService->getAll((int) $perPage, $search);

        return MeasurementHistoryResource::collection($measurementHistories)->response();
    }

    public function show(string $ulid): JsonResponse
    {
        try {
            $measurementHistory = $this->measurementHistoryService->getByUlid($ulid);

            return response()->json([
                'status' => 'success',
                'data' => new MeasurementHistoryResource($measurementHistory),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data ukuran pelanggan tidak ditemukan.',
            ], 404);
        }
    }

    public function getByCustomer(string $customerUlid): JsonResponse
    {
        $measurementHistories = $this->measurementHistoryService->getByCustomer($customerUlid);

        return response()->json([
            'status' => 'success',
            'data' => MeasurementHistoryResource::collection($measurementHistories),
        ]);
    }

    public function getByCustomerAndClothingType(string $customerUlid, string $clothingTypeUlid): JsonResponse
    {
        $measurementHistories = $this->measurementHistoryService->getByCustomerAndClothingType($customerUlid, $clothingTypeUlid);

        return response()->json([
            'status' => 'success',
            'data' => MeasurementHistoryResource::collection($measurementHistories),
        ]);
    }

    public function store(MeasurementHistoryRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $measurementHistory = $this->measurementHistoryService->create($validatedData);

        return response()->json([
            'status' => 'success',
            'message' => 'Penambahan data ukuran pelanggan berhasil.',
            'data' => new MeasurementHistoryResource($measurementHistory),
        ], 201);
    }

    public function update(MeasurementHistoryRequest $request, string $ulid): JsonResponse
    {
        try {
            $validatedData = $request->validated();

            $measurementHistory = $this->measurementHistoryService->update($ulid, $validatedData);

            return response()->json([
                'status' => 'success',
                'message' => 'Pembaruan data ukuran pelanggan berhasil.',
                'data' => new MeasurementHistoryResource($measurementHistory),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data ukuran pelanggan tidak ditemukan.',
            ], 404);
        }
    }

    public function destroy(string $ulid): JsonResponse
    {
        try {
            $this->measurementHistoryService->delete($ulid);

            return response()->json([
                'status' => 'success',
                'message' => 'Hapus data ukuran pelanggan berhasil.',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data ukuran pelanggan tidak ditemukan.',
            ], 404);
        }
    }
}