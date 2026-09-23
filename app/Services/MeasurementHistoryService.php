<?php

namespace App\Services;

use App\Repositories\MeasurementHistoryRepository;

class MeasurementHistoryService
{
    protected MeasurementHistoryRepository $measurementHistoryRepository;

    public function __construct(MeasurementHistoryRepository $measurementHistoryRepository)
    {
        $this->measurementHistoryRepository = $measurementHistoryRepository;
    }

    public function getAll(int $perPage = 10, ?string $search = null)
    {
        return $this->measurementHistoryRepository->getAll($perPage, $search);
    }

    public function getByUlid(string $ulid)
    {
        return $this->measurementHistoryRepository->getByUlid($ulid);
    }

    public function getByCustomer(string $customerUlid)
    {
        return $this->measurementHistoryRepository->getByCustomerUlid($customerUlid);
    }

    public function getByCustomerAndClothingType(string $customerUlid, string $clothingTypeUlid)
    {
        return $this->measurementHistoryRepository->getByCustomerAndClothingTypeUlid($customerUlid, $clothingTypeUlid);
    }

    public function create(array $data)
    {
        return $this->measurementHistoryRepository->create($data);
    }

    public function update(string $ulid, array $data)
    {
        return $this->measurementHistoryRepository->update($ulid, $data);
    }

    public function delete(string $ulid)
    {
        return $this->measurementHistoryRepository->delete($ulid);
    }
}