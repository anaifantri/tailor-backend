<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MaterialRequest;
use App\Http\Resources\MaterialResource;
use App\Services\MaterialService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    private $materialService;

    public function __construct(MaterialService $materialService)
    {
        $this->materialService = $materialService;
    }

    public function index(Request $request)
    {
        $fields = ['id', 'code', 'name', 'description', 'photo', 'unit'];

        $materials = $this->materialService->getAll($request->search, $fields);

        return response()->json(MaterialResource::collection($materials));
    }

    public function show(string $hashedId){
        try {
            $fields = ['id', 'code', 'name', 'description', 'photo', 'unit'];

            $material = $this->materialService->getByHashedId($hashedId, $fields);

            return response()->json(new MaterialResource(['material' => $material]));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Data bahan tidak ditemukan'
            ], 404);
        }
    }

    public function store(MaterialRequest $request)
    {
        $validateData = $request->validated();

        $material = $this->materialService->create($validateData);

        return response()->json(new MaterialResource([
            'message' => 'Pendaftaran bahan baru berhasil',
            'material' => $material
            ]), 201);
    }

    public function update(MaterialRequest $request, string $hashedId)
    {
        try {
            $validateData = $request->validated();
            
            $material = $this->materialService->update($request->id, $validateData);
            
            return response()->json(new MaterialResource($material));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Data bahan tidak ditemukan'
            ], 404);
        }
    }

    public function destroy(string $hashedId)
    {
        try {
            $this->materialService->delete($hashedId);
            return response()->json([
                'message' => 'Hapus data bahan berhasil..!!'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Data bahan tidak ditemukan'
            ], 404);
        }
    }
}
