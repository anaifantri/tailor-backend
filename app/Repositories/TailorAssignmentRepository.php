<?php

namespace App\Repositories;

use App\Models\OrderDetail;
use App\Models\TailorAssignment;

class TailorAssignmentRepository
{
    public function getAll(int $month, int $year, ?string $search = null, array $fields){
        return TailorAssignment::select($fields)->byMonthYear($month, $year)->search($search)->with(['order_detail'])->latest()->paginate(10);
    }

    public function findOrderDetail(int $id): ?OrderDetail
    {
        return OrderDetail::find($id);
    }

    public function deleteAssignmentsByOrderDetail(int $orderDetailId): void
    {
        TailorAssignment::where('order_detail_id', $orderDetailId)->delete();
    }

    public function createAssignment(array $data): TailorAssignment
    {
        return TailorAssignment::create($data);
    }

    // public function updateProductionStatus(OrderDetail $orderDetail, string $status): void
    // {
    //     $orderDetail->productionProgress()->update(['status' => $status]);
    // }
}