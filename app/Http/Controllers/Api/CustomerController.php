<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Services\CustomerService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    private $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function index(Request $request)
    {
        $fields = ['id', 'code', 'name', 'address', 'email', 'phone', 'created_at'];
		
        $perPage = $request->query('per_page', 10);
        $search = $request->query('search', null);

        $customers = $this->customerService->getAll($perPage, $search, $fields);
		
		return response()->json($customers, 200);

        // return response()->json(CustomerResource::collection($customers));
    }

    public function show(string $hashedId){
        try {
            $fields = ['id', 'code', 'name', 'address', 'email', 'phone', 'created_at'];

            $customer = $this->customerService->getByHashedId($hashedId, $fields);

            return response()->json(new CustomerResource(['customer' => $customer]));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Data pelanggan tidak ditemukan'
            ], 404);
        }
    }

    public function store(CustomerRequest $request)
    {
        $validateData = $request->validated();

        $customer = $this->customerService->create($validateData);

        return response()->json(new CustomerResource([
            'message' => 'Pendaftaran pelanggan baru berhasil',
            'customer' => $customer
            ]), 201);
    }

    public function update(CustomerRequest $request, string $hashedId)
    {
        try {
            $validateData = $request->validated();
            
            $customer = $this->customerService->update($request->id, $validateData);
            
            return response()->json(new CustomerResource($customer));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Data pelanggan tidak ditemukan'
            ], 404);
        }
    }

    public function destroy(string $hashedId)
    {
        try {
            $this->customerService->delete($hashedId);
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
