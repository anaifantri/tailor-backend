<?php

namespace App\Services;

use App\Repositories\MeasurementHistoryRepository;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

class MeasurementHistoryService
{
    private $measurementHistoryRepository;

    public function __construct(MeasurementHistoryRepository $measurementHistoryRepository)
    {
        $this->measurementHistoryRepository = $measurementHistoryRepository;
    }

    public function getAll(array $fields)
    {
        return $this->measurementHistoryRepository->getAll($fields);
    }

    public function getByHashedId(string $hashedId, array $fields)
    {
        try {
            $decryptedId = Crypt::decryptString($hashedId);
            return $this->measurementHistoryRepository->getById((int) $decryptedId, $fields ?? ['*']);
        } catch (DecryptException $e) {
            throw new \InvalidArgumentException("ID tidak valid.");
        }
    }

    public function getByCustomer(string $hashedCustomerId, array $fields)
    {
        try {
            $customerId = Crypt::decryptString($hashedCustomerId);
            return $this->measurementHistoryRepository->getByCustomer((int) $customerId, $fields ?? ['*']);
        } catch (DecryptException $e) {
            throw new \InvalidArgumentException("ID tidak valid.");
        }
    }

    public function getByCustomerAndClothingType(string $hashedCustomerId, string $hashedClothingTypeId, array $fields)
    {
        try {
            $customerId = Crypt::decryptString($hashedCustomerId);
            $clothingTypeId = Crypt::decryptString($hashedClothingTypeId);
            return $this->measurementHistoryRepository->getByCustomerAndClothingType((int) $customerId, (int) $clothingTypeId, $fields ?? ['*']);
        } catch (DecryptException $e) {
            throw new \InvalidArgumentException("ID tidak valid.");
        }
    }

    public function create(array $data)
    {
        try {
            $customerId = (int) Crypt::decryptString($data['customer_id']);
            $clothingTypeId = (int) Crypt::decryptString($data['clothing_type_id']);
        } catch (DecryptException $e) {
            throw new DecryptException("Item pakaian / penjahit / pelanggan tidak valid.");
        }
        $data['customer_id'] = $customerId;
        $data['clothing_type_id'] = $clothingTypeId;
        return $this->measurementHistoryRepository->create($data);
    }

    public function update(int $id, array $data)
    {
        return $this->measurementHistoryRepository->update($id, $data);
    }

    public function delete(string $hashedId)
    {
        $decryptedId = Crypt::decryptString($hashedId);

        return $this->measurementHistoryRepository->delete((int) $decryptedId);
    }
}