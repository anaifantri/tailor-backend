<?php

namespace App\Repositories;

use App\Models\MeasurementHistory;

class MeasurementHistoryRepository
{
    public function getAll(array $fields)
    {
        return MeasurementHistory::select($fields)->with(['customer', 'clothing_type'])->latest()->paginate(10);
    }

    public function getById(int $id, array $fields)
    {
        return MeasurementHistory::select($fields)->with(['customer', 'clothing_type'])->findOrFail($id);
    }

    public function getByCustomerAndClothing(int $customerId, int $clothingId, array $fields)
    {
        return MeasurementHistory::select($fields)->where('customer_id', $customerId)->where('clothing_type_id', $clothingId)->with(['customer', 'clothing_type'])->latest()->get();
    }

    public function create(array $data)
    {
        return MeasurementHistory::create($data);
    }

    public function update(int $id, array $data)
    {
        $measurementHistory = MeasurementHistory::findOrFail($id);

        $measurementHistory->update($data);

        return $measurementHistory;
    }

    public function delete(int $id)
    {
        $measurementHistory = MeasurementHistory::findOrFail($id);

        $measurementHistory->delete();
    }
}