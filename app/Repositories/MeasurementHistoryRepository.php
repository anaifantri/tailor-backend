<?php

namespace App\Repositories;

use App\Models\MeasurementHistory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MeasurementHistoryRepository
{
    public function getAll(int $perPage = 10, ?string $search = null): LengthAwarePaginator
    {
        return MeasurementHistory::with(['customer', 'clothing_type'])
            ->when($search, function ($query, $search) {
                $query->where('category', 'like', "%{$search}%")
                      ->orWhere('measured_by', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate($perPage);
    }

    public function getByUlid(string $ulid): MeasurementHistory
    {
        return MeasurementHistory::with(['customer', 'clothing_type'])
            ->where('ulid', $ulid)
            ->firstOrFail();
    }

    public function getByCustomerUlid(string $customerUlid)
    {
        return MeasurementHistory::with(['customer', 'clothing_type'])
            ->where('customer_ulid', $customerUlid)
            ->latest()
            ->get();
    }

    public function getByCustomerAndClothingTypeUlid(string $customerUlid, string $clothingTypeUlid)
    {
        return MeasurementHistory::with(['customer', 'clothing_type'])
            ->where('customer_ulid', $customerUlid)
            ->where('clothing_type_ulid', $clothingTypeUlid)
            ->latest()
            ->get();
    }

    public function create(array $data): MeasurementHistory
    {
        return MeasurementHistory::create($data);
    }

    public function update(string $ulid, array $data): MeasurementHistory
    {
        $measurementHistory = $this->getByUlid($ulid);
        $measurementHistory->update($data);

        return $measurementHistory->fresh(['customer', 'clothing_type']);
    }

    public function delete(string $ulid): bool
    {
        $measurementHistory = $this->getByUlid($ulid);
        return $measurementHistory->delete();
    }
}