<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClothingTypeRequest;
use App\Http\Resources\ClothingTypeResource;
use App\Services\ClothingTypeService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ClothingTypeController extends Controller
{
    private $clothingTypeService;

    public function __construct(ClothingTypeService $clothingTypeService)
    {
        $this->clothingTypeService = $clothingTypeService;
    }

    public function index(Request $request)
    {
        $fields = ['id', 'code', 'type', 'base_price'];

        $clothingTypes = $this->clothingTypeService->getAll($request->search, $fields);

        return response()->json(ClothingTypeResource::collection($clothingTypes));
    }

    public function show(string $hashedId){
        try {
            $fields = ['id', 'code', 'type', 'base_price'];

            $clothingType = $this->clothingTypeService->getByHashedId($hashedId, $fields);

            return response()->json(new ClothingTypeResource(['clothing_type' => $clothingType]));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Data jenis pakaian tidak ditemukan'
            ], 404);
        }
    }

    public function store(ClothingTypeRequest $request)
    {
        $validateData = $request->validated();

        $clothingType = $this->clothingTypeService->create($validateData);

        return response()->json(new ClothingTypeResource([
            'message' => 'Pendaftaran jenis pakaian baru berhasil',
            'clothing_type' => $clothingType
            ]), 201);
    }

    public function update(ClothingTypeRequest $request, string $hashedId)
    {
        try {
            $validateData = $request->validated();
            
            $clothingType = $this->clothingTypeService->update($request->id, $validateData);
            
            return response()->json(new ClothingTypeResource($clothingType));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Data jenis pakaian tidak ditemukan'
            ], 404);
        }
    }

    public function destroy(string $hashedId)
    {
        try {
            $this->clothingTypeService->delete($hashedId);
            return response()->json([
                'message' => 'Hapus data jenis pakaian berhasil..!!'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Data jenis pakaian tidak ditemukan'
            ], 404);
        }
    }
}
