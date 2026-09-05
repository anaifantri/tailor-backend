<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(Request $request)
    {
        $fields = ['id', 'number', 'user_id', 'client_id', 'order_date', 'fitting_date', 'due_date', 'tax', 'total', 'created_at'];

        $orders = $this->orderService->getAll($request->month, $request->year, $request->search, $fields);

        return response()->json(OrderResource::collection($orders));
    }

    public function unpaid(Request $request)
    {
        $fields = ['id', 'number', 'user_id', 'client_id', 'order_date', 'fitting_date', 'due_date', 'tax', 'total', 'created_at'];

        $orders = $this->orderService->getUnpaid($request->search, $fields);

        return response()->json(OrderResource::collection($orders));
    }

    public function show(string $hashedId){
        try {
            $fields = ['id', 'number', 'user_id', 'client_id', 'order_date', 'fitting_date', 'due_date', 'tax', 'total', 'created_at'];

            $order = $this->orderService->getByHashedId($hashedId, $fields);

            return response()->json(new OrderResource(['order' => $order]));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Data pesanan tidak ditemukan'
            ], 404);
        }
    }

    public function store(OrderRequest $request)
    {
        $validateData = $request->validated();

        $order = $this->orderService->create($validateData);

        return response()->json(new OrderResource([
            'message' => 'Pendaftaran pesanan baru berhasil',
            'order' => $order
            ]), 201);
    }

    public function update(OrderRequest $request, string $hashedId)
    {
        try {
            $validateData = $request->validated();
            
            $order = $this->orderService->update($hashedId, $validateData);
            
            return response()->json(new OrderResource($order));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Data pesanan tidak ditemukan'
            ], 404);
        }
    }

    public function destroy(string $hashedId)
    {
        try {
            $this->orderService->delete($hashedId);
            return response()->json([
                'message' => 'Hapus data pesanan berhasil..!!'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Data pesanan tidak ditemukan'
            ], 404);
        }
    }
}
