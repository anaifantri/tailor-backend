<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Services\PaymentService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 10);
        $search  = $request->query('search');
        $month   = $request->query('month');
        $year    = $request->query('year');

        $payments = $this->paymentService->getAll($perPage, $month, $year, $search);

        return response()->json([
            'status' => 'success',
            'data'   => PaymentResource::collection($payments),
            'meta'   => [
                'current_page' => $payments->currentPage(),
                'last_page'    => $payments->lastPage(),
                'per_page'     => $payments->perPage(),
                'total'        => $payments->total(),
            ],
        ]);
    }

    public function show(string $ulid): JsonResponse
    {
        try {
            $payment = $this->paymentService->getByUlid($ulid);

            return response()->json([
                'status' => 'success',
                'data'   => new PaymentResource($payment),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data pembayaran tidak ditemukan.',
            ], 404);
        }
    }

    public function store(PaymentRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $payment = $this->paymentService->create($validatedData);

        return response()->json([
            'status'  => 'success',
            'message' => 'Input data pembayaran berhasil.',
            'data'    => new PaymentResource($payment),
        ], 201);
    }

    public function update(PaymentRequest $request, string $ulid): JsonResponse
    {
        try {
            $validatedData = $request->validated();

            $payment = $this->paymentService->update($ulid, $validatedData);

            return response()->json([
                'status'  => 'success',
                'message' => 'Edit data pembayaran berhasil.',
                'data'    => new PaymentResource($payment),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data pembayaran tidak ditemukan.',
            ], 404);
        }
    }

    public function destroy(string $ulid): JsonResponse
    {
        try {
            $this->paymentService->delete($ulid);

            return response()->json([
                'status'  => 'success',
                'message' => 'Hapus data pembayaran berhasil.',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data pembayaran tidak ditemukan.',
            ], 404);
        }
    }
}