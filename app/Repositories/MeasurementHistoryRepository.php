<?php

namespace App\Repositories;

use App\Models\MeasurementHistory;

class MeasurementHistoryRepository
{
    public function getAll(array $fields)
    {
        return MeasurementHistory::select($fields)->latest()->paginate(10);
    }

    public function getById(int $id, array $fields)
    {
        return MeasurementHistory::select($fields)->findOrFail($id);
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