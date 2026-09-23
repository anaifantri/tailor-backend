<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderDetailDeliveryRequest;
use App\Http\Resources\OrderDetailDeliveryResource;
use App\Services\OrderDetailDeliveryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderDetailDeliveryController extends Controller
{
    public function __construct(
        protected OrderDetailDeliveryService $deliveryService
    ) {}

    /**
     * Menampilkan daftar pengiriman (dengan paginasi dan filter).
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = $request->input('per_page', 10);
        $month = $request->input('month');
        $year = $request->input('year', date('Y'));
        $search = $request->input('search');

        $deliveries = $this->deliveryService->getAllDeliveries(
            (int) $perPage,
            $month ? (int) $month : null,
            (int) $year,
            $search
        );

        return OrderDetailDeliveryResource::collection($deliveries)
            ->additional([
                'status'  => 'success',
                'message' => 'Berhasil mengambil data pengambilan / pengiriman pesanan',
            ]);
    }

    /**
     * Menyimpan data pengiriman/penyerahan barang baru.
     */
    public function store(OrderDetailDeliveryRequest $request): JsonResponse
    {
        $delivery = $this->deliveryService->createDelivery($request->validated());

        return (new OrderDetailDeliveryResource($delivery))
            ->additional([
                'status'  => 'success',
                'message' => 'Data pengambilan / pengiriman berhasil ditambahkan',
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Menampilkan detail pengiriman berdasarkan ID.
     */
    public function show(string $hashed_id): JsonResponse
    {
        // $id = $request->input('id');
        $delivery = $this->deliveryService->getDeliveryById($hashed_id);

        return (new OrderDetailDeliveryResource($delivery))
            ->additional([
                'status'  => 'success',
                'message' => 'Detail pengambilan / pengiriman berhasil ditemukan',
            ])
            ->response();
    }

    /**
     * Memperbarui data pengiriman.
     */
    public function update(OrderDetailDeliveryRequest $request): JsonResponse
    {
        $id = $request->input('id');
        $delivery = $this->deliveryService->updateDelivery($id, $request->validated());

        return (new OrderDetailDeliveryResource($delivery))
            ->additional([
                'status'  => 'success',
                'message' => 'Data pengambilan / pengiriman berhasil diperbarui',
            ])
            ->response();
    }

    /**
     * Menghapus data pengiriman.
     */
    public function destroy(string $hashed_id): JsonResponse
    {
        $this->deliveryService->deleteDelivery($hashed_id);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data pengambilan / pengiriman berhasil dihapus',
        ]);
    }
}
