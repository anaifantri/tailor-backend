<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClientRequest;
use App\Http\Resources\ClientResource;
use App\Services\ClientService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    private $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    public function index(Request $request)
    {
        $fields = ['id', 'code', 'name', 'address', 'email', 'phone', 'created_at'];

        $clients = $this->clientService->getAll($request->search, $fields);

        return response()->json(ClientResource::collection($clients));
    }

    public function show(string $hashedId){
        try {
            $fields = ['id', 'code', 'name', 'address', 'email', 'phone', 'created_at'];

            $client = $this->clientService->getByHashedId($hashedId, $fields);

            return response()->json(new ClientResource(['client' => $client]));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Data pelanggan tidak ditemukan'
            ], 404);
        }
    }

    public function store(ClientRequest $request)
    {
        $validateData = $request->validated();

        $client = $this->clientService->create($validateData);

        return response()->json(new ClientResource([
            'message' => 'Pendaftaran pelanggan baru berhasil',
            'client' => $client
            ]), 201);
    }

    public function update(ClientRequest $request, string $hashedId)
    {
        try {
            $validateData = $request->validated();
            
            $client = $this->clientService->update($request->id, $validateData);
            
            return response()->json(new ClientResource($client));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Data pelanggan tidak ditemukan'
            ], 404);
        }
    }

    public function destroy(string $hashedId)
    {
        try {
            $this->clientService->delete($hashedId);
            return response()->json([
                'message' => 'Hapus data pelanggan berhasil..!!'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Data pelanggan tidak ditemukan'
            ], 404);
        }
    }
}
