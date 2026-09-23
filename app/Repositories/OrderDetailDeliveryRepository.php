<?php

namespace App\Repositories;

use App\Models\OrderDetailDelivery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class OrderDetailDeliveryRepository
{
    /**
     * Memastikan kolom id dan foreign keys selalu terambil agar relasi & hashed_id tidak broken.
     */
    private function ensureEssentialFields(array $fields): array
    {
        if (in_array('*', $fields)) {
            return $fields;
        }

        return array_unique(array_merge($fields, ['id', 'order_detail_id', 'user_id']));
    }

    public function getAll(
        int $perPage = 10,
        ?int $month = null,
        ?int $year = null,
        ?string $search = null,
        array $fields = ['*']
    ): LengthAwarePaginator {
        $year = $year ?? (int) date('Y');

        return OrderDetailDelivery::select($this->ensureEssentialFields($fields))
            ->byMonthYear($month, $year)
            ->search($search)
            ->with(['order_detail.clothing_type', 'user'])
            ->latest()
            ->paginate($perPage);
    }

    public function findById(int $id, array $relations = []): OrderDetailDelivery
    {
        return OrderDetailDelivery::with($relations)->findOrFail($id);
    }

    public function getByOrderDetail(int $orderDetailId, array $fields = ['*']): Collection
    {
        return OrderDetailDelivery::select($this->ensureEssentialFields($fields))
            ->where('order_detail_id', $orderDetailId)
            ->with(['order_detail', 'user'])
            ->latest()
            ->get();
    }

    public function create(array $data): OrderDetailDelivery
    {
        return OrderDetailDelivery::create($data);
    }

    public function update(int $id, array $data): OrderDetailDelivery
    {
        $orderDetailDelivery = $this->findById($id);
        $orderDetailDelivery->update($data);

        return $orderDetailDelivery->refresh();
    }

    public function delete(int $id): bool
    {
        $orderDetailDelivery = $this->findById($id);

        return (bool) $orderDetailDelivery->delete();
    }
}