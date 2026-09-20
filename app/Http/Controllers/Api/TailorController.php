<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TailorRequest;
use App\Http\Resources\TailorResource;
use App\Services\TailorService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class TailorController extends Controller
{
    private $tailorService;

    public function __construct(TailorService $tailorService)
    {
        $this->tailorService = $tailorService;
    }

    public function index(Request $request)
    {
        $fields = ['id', 'code', 'specialty', 'photo', 'name', 'address', 'email', 'phone', 'is_active'];
		
        $perPage = $request->query('per_page', 10);
        $search = $request->query('search', null);

        $tailors = $this->tailorService->getAll($perPage, $search, $fields);
		
		return response()->json($tailors, 200);

       // return response()->json(TailorResource::collection($tailors));
    }

    public function show(string $hashedId){
        try {
            $fields = ['id', 'code', 'specialty', 'photo', 'name', 'address', 'email', 'phone', 'is_active'];

            $tailor = $this->tailorService->getByHashedId($hashedId, $fields);

            return response()->json(new TailorResource(['tailor' => $tailor]));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Data tukang jahit tidak ditemukan'
            ], 404);
        }
    }

    public function store(TailorRequest $request)
    {
        $validateData = $request->validated();

        $tailor = $this->tailorService->create($validateData);

        return response()->json(new TailorResource([
            'message' => 'Pendaftaran tukang jahit baru berhasil',
            'tailor' => $tailor
            ]), 201);
    }

    public function update(TailorRequest $request, string $hashedId)
    {
        try {
            $validateData = $request->validated();
            
            $tailor = $this->tailorService->update($request->id, $validateData);
            
            return response()->json(new TailorResource($tailor));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Data tukang jahit tidak ditemukan'
            ], 404);
        }
    }

    public function destroy(string $hashedId)
    {
        try {
            $this->tailorService->delete($hashedId);
            return response()->json([
                'message' => 'Hapus data tukang jahit berhasil..!!'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Data tukang jahit tidak ditemukan'
            ], 404);
        }
    }
}
