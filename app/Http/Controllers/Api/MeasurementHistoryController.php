<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MeasurementHistoryRequest;
use App\Http\Resources\MeasurementHistoryResource;
use App\Services\MeasurementHistoryService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class MeasurementHistoryController extends Controller
{
    private $measurementHistoryService;

    public function __construct(MeasurementHistoryService $measurementHistoryService)
    {
        $this->measurementHistoryService = $measurementHistoryService;
    }

    public function index()
    {
        $fields = ['id', 'category', 'customer_id', 'clothing_type_id', 'measured_at', 'measured_by', 'notes', 'measurement_details'];

        $measurementHistories = $this->measurementHistoryService->getAll($fields);

        return response()->json(MeasurementHistoryResource::collection($measurementHistories));
    }

    public function show(string $hashedId){
        try {
            $fields = ['id', 'category', 'customer_id', 'clothing_type_id', 'measured_at', 'measured_by', 'notes', 'measurement_details'];

            $measurementHistory = $this->measurementHistoryService->getByHashedId($hashedId, $fields);

            return response()->json(new MeasurementHistoryResource(['measurement_history' => $measurementHistory]));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Data ukuran pelanggan tidak ditemukan'
            ], 404);
        }
    }

    public function getByCustomer(Request $request){
        try {
            $fields = ['id', 'category', 'customer_id', 'clothing_type_id', 'measured_at', 'measured_by', 'notes', 'measurement_details'];

            $measurementHistories = $this->measurementHistoryService->getByCustomerAndClothing($request->customerId, $fields);

            return response()->json(new MeasurementHistoryResource(['measurement_histories' => $measurementHistories]));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Data ukuran pelanggan tidak ditemukan'
            ], 404);
        }
    }

    public function getByCustomerAndClothingType(Request $request){
        try {
            $fields = ['id', 'category', 'customer_id', 'clothing_type_id', 'measured_at', 'measured_by', 'notes', 'measurement_details'];

            $measurementHistories = $this->measurementHistoryService->getByCustomerAndClothingType($request->customerId, $request->clothingTypeId, $fields);

            return response()->json(new MeasurementHistoryResource(['measurement_histories' => $measurementHistories]));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Data ukuran pelanggan tidak ditemukan'
            ], 404);
        }
    }

    public function store(MeasurementHistoryRequest $request)
    {
        $validateData = $request->validated();

        $measurementHistory = $this->measurementHistoryService->create($validateData);

        return response()->json(new MeasurementHistoryResource([
            'message' => 'Penambahan data ukuran pelanggan berhasil',
            'measurement_history' => $measurementHistory
            ]), 201);
    }

    public function update(MeasurementHistoryRequest $request, string $hashedId)
    {
        try {
            $validateData = $request->validated();
            
            $measurementHistory = $this->measurementHistoryService->update($request->id, $validateData);
            
            return response()->json(new MeasurementHistoryResource($measurementHistory));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Data ukuran pelanggan tidak ditemukan'
            ], 404);
        }
    }

    public function destroy(string $hashedId)
    {
        try {
            $this->measurementHistoryService->delete($hashedId);
            return response()->json([
                'message' => 'Hapus data ukuran pelanggan berhasil..!!'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Data ukuran pelanggan tidak ditemukan'
            ], 404);
        }
    }
}
