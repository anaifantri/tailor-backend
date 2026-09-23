<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderCancellationRequest;
use App\Services\OrderCancellationService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class OrderCancellationController extends Controller
{
    public function __construct(
        protected OrderCancellationService $service
    ) {}

    /**
     * Display a listing of order cancellations.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = (int) $request->query('per_page', 10);
            $month = $request->query('month');
            $year = $request->query('year') ? (int) $request->query('year') : null;
            $search = $request->query('search');

            $cancellations = $this->service->getAllAssignments($perPage, $month, $year, $search);

            return response()->json([
                'status'  => 'success',
                'message' => 'Data pembatalan order berhasil diambil.',
                'data'    => $cancellations,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal mengambil data pembatalan order.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created order cancellation in storage.
     */
    public function store(OrderCancellationRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $cancellation = $this->service->create($validated);

            return response()->json([
                'status'  => 'success',
                'message' => 'Pembatalan order berhasil dibuat.',
                'data'    => $cancellation,
            ], 201);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal membuat pembatalan order.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified order cancellation.
     */
    public function show(mixed $id): JsonResponse
    {
        try {
            $cancellation = $this->service->getById($id);

            return response()->json([
                'status'  => 'success',
                'message' => 'Detail pembatalan order berhasil ditemukan.',
                'data'    => $cancellation,
            ], 200);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data pembatalan order tidak ditemukan atau terjadi kesalahan.',
            ], 4404);
        }
    }

    /**
     * Update the specified order cancellation in storage.
     */
    public function update(OrderCancellationRequest $request, mixed $id): JsonResponse
    {
        try {
            $validated = $request->validated();
            $cancellation = $this->service->update($id, $validated);

            return response()->json([
                'status'  => 'success',
                'message' => 'Pembatalan order berhasil diperbarui.',
                'data'    => $cancellation,
            ], 200);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal memperbarui pembatalan order.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified order cancellation from storage.
     */
    public function destroy(mixed $id): JsonResponse
    {
        try {
            $this->service->delete($id);

            return response()->json([
                'status'  => 'success',
                'message' => 'Pembatalan order berhasil dihapus.',
            ], 200);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal menghapus pembatalan order.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get cancellations by order_detail_id.
     */
    public function getByOrderDetail(mixed $orderDetailId): JsonResponse
    {
        try {
            $cancellations = $this->service->getByOrderDetail($orderDetailId);

            return response()->json([
                'status'  => 'success',
                'message' => 'Data pembatalan berdasarkan detail order berhasil diambil.',
                'data'    => $cancellations,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get cancellations by order_id.
     */
    public function getByOrder(mixed $orderId): JsonResponse
    {
        try {
            $cancellations = $this->service->getByOrder($orderId);

            return response()->json([
                'status'  => 'success',
                'message' => 'Data pembatalan berdasarkan order berhasil diambil.',
                'data'    => $cancellations,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
