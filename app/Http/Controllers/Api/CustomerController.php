<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Services\CustomerService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    private CustomerService $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function index(Request $request): JsonResponse
    {
        $fields = ['ulid', 'code', 'name', 'address', 'email', 'phone', 'created_at'];

        $perPage = (int) $request->query('per_page', 10);
        $search = $request->query('search');

        $customers = $this->customerService->getAll($perPage, $search, $fields);

        return response()->json($customers, 200);
    }

    public function show(string $ulid): JsonResponse
    {
        try {
            $fields = ['ulid', 'code', 'name', 'address', 'email', 'phone', 'created_at'];

            $customer = $this->customerService->getByUlid($ulid, $fields);

            return response()->json(new CustomerResource(['customer' => $customer]), 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Data pelanggan tidak ditemukan'
            ], 404);
        }
    }

    public function store(CustomerRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $customer = $this->customerService->create($validatedData);

        return response()->json(new CustomerResource([
            'message' => 'Pendaftaran pelanggan baru berhasil',
            'customer' => $customer
        ]), 201);
    }

    public function update(CustomerRequest $request, string $ulid): JsonResponse
    {
        try {
            $validatedData = $request->validated();

            $customer = $this->customerService->update($ulid, $validatedData);

            return response()->json(new CustomerResource($customer), 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Data pelanggan tidak ditemukan'
            ], 404);
        }
    }

    public function destroy(string $ulid): JsonResponse
    {
        try {
            $this->customerService->delete($ulid);

            return response()->json([
                'message' => 'Hapus data pelanggan berhasil..!!'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Data pelanggan tidak ditemukan'
            ], 404);
        }
    }
}