<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Services\PaymentService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    private $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index(Request $request)
    {
        $fields = ['id', 'user_id', 'order_id', 'payment_date', 'amount_paid', 'payment_method', 'payment_status', 'notes', 'created_at'];
		
		
        $perPage = $request->query('per_page', 10);
        $search = $request->query('search', null);

        $payments = $this->paymentService->getAll($perPage, $request->month, $request->year, $request->search, $fields);
		
		return response()->json($payments, 200);

        // return response()->json(PaymentResource::collection($payments));
    }

    public function show(string $hashedId){
        try {
            $fields = ['id', 'user_id', 'order_id', 'payment_date', 'amount_paid', 'payment_method', 'payment_status', 'notes', 'created_at'];

            $payment = $this->paymentService->getByHashedId($hashedId, $fields);

            return response()->json(new PaymentResource(['payment' => $payment]));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Data pembayaran tidak ditemukan'
            ], 404);
        }
    }

    public function store(PaymentRequest $request)
    {
        $validateData = $request->validated();

        $payment = $this->paymentService->create($validateData);

        return response()->json(new PaymentResource([
            'message' => 'Input data pembayaran berhasil',
            'payment' => $payment
            ]), 201);
    }

    public function update(PaymentRequest $request, string $hashedId)
    {
        try {
            $validateData = $request->validated();
            
            $payment = $this->paymentService->update($request->id, $validateData);
            
            return response()->json(new PaymentResource([
			'payment' => $payment,
			'message' => 'Edit data pembayaran berhasil']));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Data pembayaran tidak ditemukan'
            ], 404);
        }
    }

    public function destroy(string $hashedId)
    {
        try {
            $this->paymentService->delete($hashedId);
            return response()->json([
                'message' => 'Hapus data pembayaran berhasil..!!'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Data pembayaran tidak ditemukan'
            ], 404);
        }
    }
}
