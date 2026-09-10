<?php

namespace App\Services;

use App\Repositories\TailorAssignmentRepository;
use Exception;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class TailorAssignmentService
{
    protected TailorAssignmentRepository $tailorAssignmentRepository;

    public function __construct(TailorAssignmentRepository $tailorAssignmentRepository)
    {
        $this->tailorAssignmentRepository = $tailorAssignmentRepository;
    }

    public function getAll(int $month, int $year, ?string $search = null, array $fields)
    {
        return $this->tailorAssignmentRepository->getAll($month, $year, $search, $fields);
    }

    public function assignLabor(array $payload): mixed
    {
        try {
            $orderDetailId = (int) Crypt::decryptString($payload['order_detail_id']);
        } catch (DecryptException $e) {
            throw new DecryptException("Item pakaian tidak valid.");
        }

        $orderDetail = $this->tailorAssignmentRepository->findOrderDetail($orderDetailId);
        if (!$orderDetail) {
            throw new Exception("Detail pesanan tidak ditemukan.");
        }

        $totalQuantityInput = $payload['quantity_assigned'];

        if ($totalQuantityInput > $orderDetail->quantity) {
            throw new Exception("Total kuantitas borongan melebihi jumlah pesanan asli ({$orderDetail->quantity} pcs).");
        }

        return DB::transaction(function () use ($payload, $orderDetail, $orderDetailId) {
            $this->tailorAssignmentRepository->deleteAssignmentsByOrderDetail($orderDetailId);

            // foreach ($payload['tailor_assignments'] as $assignment) {
                try {
                    $tailorId = (int) Crypt::decryptString($payload['tailor_id']);
                } catch (DecryptException $e) {
                    throw new DecryptException("Staf penjahit tidak valid.");
                }

                $quantity = (int) $payload['quantity_assigned'];
                $cost = (int) $payload['labor_cost'];
                $assignmentDate = $payload['assignment_date'];

                $this->tailorAssignmentRepository->createAssignment([
                    'order_detail_id'    => $orderDetailId,
                    'tailor_id'          => $tailorId,
                    'assignment_date'  => $assignmentDate,
                    'quantity_assigned'  => $quantity,
                    'labor_cost' => $cost,
                    'total_labor_cost'   => $quantity * $cost, 
                    'status'             => 'queued'
                ]);
            // }

            // Update status utama ke pengerjaan 'sewing'
            // $this->tailorAssignmentRepository->updateProductionStatus($orderDetail, 'sewing');

            return $orderDetail->load('tailor_assignments.tailor');
        });
    }
}