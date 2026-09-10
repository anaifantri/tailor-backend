<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TailorAssignmentRequest;
use App\Http\Resources\TailorAssignmentResource;
use App\Services\TailorAssignmentService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class TailorAssignmentController extends Controller
{
    protected TailorAssignmentService $tailorAssignmentService;

    public function __construct(TailorAssignmentService $tailorAssignmentService)
    {
        $this->tailorAssignmentService = $tailorAssignmentService;
    }

    public function index(Request $request)
    {
        $fields = ['id', 'user_id', 'order_id', 'payment_date', 'amount_paid', 'payment_method', 'payment_status', 'notes', 'created_at'];

        $payments = $this->tailorAssignmentService->getAll($request->month, $request->year, $request->search, $fields);

        return response()->json(TailorAssignmentResource::collection($payments));
    }
    
    public function store(TailorAssignmentRequest $request)
    {
        try {
            $orderDetail = $this->tailorAssignmentService->assignLabor($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Pembagian kerja borongan berhasil disimpan.',
                'data'    => TailorAssignmentResource::collection($orderDetail->tailor_assignments)
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
