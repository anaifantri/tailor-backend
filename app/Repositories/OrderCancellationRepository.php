<?php

namespace App\Repositories;

use App\Models\OrderCancellation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class OrderCancellationRepository
{
    public function getAll(
        int $perPage = 10,
        ?int $month = null,
        ?int $year = null,
        ?string $search = null,
        array $fields = ['*']
    ): LengthAwarePaginator {
        $year = $year ?? (int) date('Y');

        return OrderCancellation::select($fields)
            ->byMonthYear($month, $year)
            ->search($search)
            ->with(['order_detail.clothing_type', 'user'])
            ->latest()
            ->paginate($perPage);
    }

    public function findById(int $id, array $relations = []): OrderCancellation
    {
        return OrderCancellation::with($relations)->findOrFail($id);
    }

    public function getByOrderDetail(int $orderDetailId, array $fields = ['*']): Collection
    {
        return OrderCancellation::select($fields)
            ->where('order_detail_id', $orderDetailId)
            ->with(['order_detail', 'user', 'order'])
            ->latest()
            ->get();
    }

    public function getByOrder(int $orderId, array $fields = ['*']): Collection
    {
        return OrderCancellation::select($fields)
            ->where('order_id', $orderId)
            ->with(['order_detail', 'user', 'order'])
            ->latest()
            ->get();
    }

    public function create(array $data): OrderCancellation
    {
        return OrderCancellation::create($data);
    }

    public function update(int $id, array $data): OrderCancellation
    {
        $orderCancellation = $this->findById($id);
        $orderCancellation->update($data);

        return $orderCancellation->refresh();
    }

    public function delete(int $id): bool
    {
        $orderCancellation = $this->findById($id);

        return (bool) $orderCancellation->delete();
    }
}