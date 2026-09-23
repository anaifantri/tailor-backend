<?php

namespace App\Repositories;

use App\Models\ProductionProgress;
use Illuminate\Database\Eloquent\Collection;

class ProductionProgressRepository
{
    public function getByOrderDetailUlid(string $orderDetailUlid): Collection
    {
        return ProductionProgress::with(['orderDetail'])
            ->where('order_detail_ulid', $orderDetailUlid)
            ->latest()
            ->get();
    }

    public function getByUlid(string $ulid): ProductionProgress
    {
        return ProductionProgress::with(['orderDetail'])
            ->where('ulid', $ulid)
            ->firstOrFail();
    }

    public function create(array $data): ProductionProgress
    {
        return ProductionProgress::create($data);
    }

    public function update(string $ulid, array $data): ProductionProgress
    {
        $productionProgress = $this->getByUlid($ulid);
        $productionProgress->update($data);

        return $productionProgress->fresh(['orderDetail']);
    }

    public function delete(string $ulid): bool
    {
        $productionProgress = $this->getByUlid($ulid);
        return $productionProgress->delete();
    }
}