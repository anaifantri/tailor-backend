<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = $request->query('per_page', 10);
        $search = $request->query('search');
        $month = $request->query('month');
        $year = $request->query('year');

        $orders = $this->orderService->getAll((int) $perPage, $month, $year ? (int) $year : null, $search);

        return OrderResource::collection($orders)->response();
    }

    public function getBySearch(Request $request): JsonResponse
    {
        $orders = $this->orderService->getBySearch($request->query('search'));

        return response()->json([
            'status' => 'success',
            'data' => OrderResource::collection($orders),
        ]);
    }

    public function unpaid(Request $request): JsonResponse
    {
        $orders = $this->orderService->getUnpaid($request->query('search'));

        return response()->json([
            'status' => 'success',
            'data' => OrderResource::collection($orders),
        ]);
    }

    public function show(string $ulid): JsonResponse
    {
        try {
            $order = $this->orderService->getByUlid($ulid);

            return response()->json([
                'status' => 'success',
                'data' => new OrderResource($order),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data pesanan tidak ditemukan.',
            ], 404);
        }
    }

    public function store(OrderRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $order = $this->orderService->create($validatedData);

        return response()->json([
            'status' => 'success',
            'message' => 'Pendaftaran pesanan baru berhasil.',
            'data' => new OrderResource($order),
        ], 201);
    }

    public function update(OrderRequest $request, string $ulid): JsonResponse
    {
        try {
            $validatedData = $request->validated();

            $order = $this->orderService->update($ulid, $validatedData);

            return response()->json([
                'status' => 'success',
                'message' => 'Pembaruan data pesanan berhasil.',
                'data' => new OrderResource($order),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data pesanan tidak ditemukan.',
            ], 404);
        }
    }

    public function destroy(string $ulid): JsonResponse
    {
        try {
            $this->orderService->delete($ulid);

            return response()->json([
                'status' => 'success',
                'message' => 'Hapus data pesanan berhasil.',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data pesanan tidak ditemukan.',
            ], 404);
        }
    }
}