<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TailorRequest;
use App\Http\Resources\TailorResource;
use App\Services\TailorService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TailorController extends Controller
{
    private TailorService $tailorService;

    public function __construct(TailorService $tailorService)
    {
        $this->tailorService = $tailorService;
    }

    public function index(Request $request): JsonResponse
    {
        $fields = ['ulid', 'code', 'specialty', 'photo', 'name', 'address', 'email', 'phone', 'is_active'];

        $perPage = (int) $request->query('per_page', 10);
        $search = $request->query('search');

        $tailors = $this->tailorService->getAll($perPage, $search, $fields);

        return response()->json($tailors, 200);
    }

    public function show(string $ulid): JsonResponse
    {
        try {
            $fields = ['ulid', 'code', 'specialty', 'photo', 'name', 'address', 'email', 'phone', 'is_active'];

            $tailor = $this->tailorService->getByUlid($ulid, $fields);

            return response()->json(new TailorResource(['tailor' => $tailor]), 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Data tukang jahit tidak ditemukan'
            ], 404);
        }
    }

    public function store(TailorRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $tailor = $this->tailorService->create($validatedData);

        return response()->json(new TailorResource([
            'message' => 'Pendaftaran tukang jahit baru berhasil',
            'tailor' => $tailor
        ]), 201);
    }

    public function update(TailorRequest $request, string $ulid): JsonResponse
    {
        try {
            $validatedData = $request->validated();

            $tailor = $this->tailorService->update($ulid, $validatedData);

            return response()->json(new TailorResource($tailor), 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Data tukang jahit tidak ditemukan'
            ], 404);
        }
    }

    public function destroy(string $ulid): JsonResponse
    {
        try {
            $this->tailorService->delete($ulid);

            return response()->json([
                'message' => 'Hapus data tukang jahit berhasil..!!'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Data tukang jahit tidak ditemukan'
            ], 404);
        }
    }
}