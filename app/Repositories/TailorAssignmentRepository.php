<?php

namespace App\Repositories;

use App\Models\TailorAssignment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class TailorAssignmentRepository
{
    public function getAll(
        int $perPage = 10,
        ?int $month = null,
        ?int $year = null,
        ?string $search = null,
        array $fields = ['*']
    ): LengthAwarePaginator {
        $year = $year ?? (int) date('Y');

        return TailorAssignment::select($fields)
            ->byMonthYear($month, $year)
            ->search($search)
            ->with(['order_detail.clothing_type', 'tailor'])
            ->latest()
            ->paginate($perPage);
    }

    public function findById(int $id, array $relations = []): TailorAssignment
    {
        return TailorAssignment::with($relations)->findOrFail($id);
    }

    public function getByOrderDetail(int $orderDetailId, array $fields = ['*']): Collection
    {
        return TailorAssignment::select($fields)
            ->where('order_detail_id', $orderDetailId)
            ->with(['order_detail', 'tailor'])
            ->latest()
            ->get();
    }

    public function create(array $data): TailorAssignment
    {
        return TailorAssignment::create($data);
    }

    public function update(int $id, array $data): TailorAssignment
    {
        $tailorAssignment = $this->findById($id);
        $tailorAssignment->update($data);

        return $tailorAssignment->refresh();
    }

    public function updateStatus(int $id, string $status): TailorAssignment
    {
        $tailorAssignment = $this->findById($id);
        $tailorAssignment->update(['status' => $status]);

        return $tailorAssignment->refresh();
    }

    public function delete(int $id): bool
    {
        $tailorAssignment = $this->findById($id);

        return (bool) $tailorAssignment->delete();
    }
}