<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductionProgressRequest;
use App\Http\Resources\ProductionProgressResource;
use App\Services\ProductionProgressService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductionProgressController extends Controller
{
    private $productionProgressService;

    public function __construct(ProductionProgressService $productionProgressService)
    {
        $this->productionProgressService = $productionProgressService;
    }

    public function show(string $hashedId){
        try {
            $fields = ['id', 'tailor_id', 'order_detail_id', 'status', 'notes', 'created_at'];

            $productionProgress = $this->productionProgressService->getByHashedId($hashedId, $fields);

            return response()->json(new ProductionProgressResource(['production_progress' => $productionProgress]));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Data progress pengerjaan tidak ditemukan'
            ], 404);
        }
    }

    public function store(ProductionProgressRequest $request)
    {
        $validateData = $request->validated();

        $productionProgress = $this->productionProgressService->create($validateData);

        return response()->json(new ProductionProgressResource([
            'message' => 'Progress Pengerjaan berhasil ditambahkan',
            'production_progress' => $productionProgress
            ]), 201);
    }

    public function update(ProductionProgressRequest $request, string $hashedId)
    {
        try {
            $validateData = $request->validated();
            
            $productionProgress = $this->productionProgressService->update($request->id, $validateData);
            
            return response()->json(new ProductionProgressResource($productionProgress));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Data progress pengerjaan tidak ditemukan'
            ], 404);
        }
    }

    public function destroy(string $hashedId)
    {
        try {
            $this->productionProgressService->delete($hashedId);
            return response()->json([
                'message' => 'Hapus data progress pengerjaan berhasil..!!'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Data progress pengerjaan tidak ditemukan'
            ], 404);
        }
    }
}
