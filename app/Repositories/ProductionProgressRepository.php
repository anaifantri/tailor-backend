<?php

namespace App\Repositories;

use App\Models\ProductionProgress;

class ProductionProgressRepository
{
    public function getByOrderDetail(int $orderDetailId)
    {
        return ProductionProgress::where('order_detail_id', $orderDetailId)->latest();
    }

    public function getById(int $id, array $fields)
    {
        return ProductionProgress::select($fields)->with(['order_detail'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return ProductionProgress::create($data);
    }

    public function update(int $id, array $data)
    {
        $productionProgress = ProductionProgress::findOrFail($id);

        $productionProgress->update($data);

        return $productionProgress;
    }

    public function delete(int $id)
    {
        $productionProgress = ProductionProgress::findOrFail($id);

        $productionProgress->delete();
    }
}