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

    public function getByCustomerAndClothing(string $hashedCustomerId, string $hashedClothingId, array $fields)
    {
        try {
            $customerId = Crypt::decryptString($hashedCustomerId);
            $clothingId = Crypt::decryptString($hashedClothingId);
            return $this->measurementHistoryRepository->getByCustomerAndClothing((int) $customerId,(int) $clothingId, $fields ?? ['*']);
        } catch (DecryptException $e) {
            throw new \InvalidArgumentException("ID tidak valid.");
        }
    }

    public function create(array $data)
    {
        try {
            $clothingTypeId = (int) Crypt::decryptString($data['clothing_type_id']);
            $customerId = (int) Crypt::decryptString($data['customer_id']);
        } catch (DecryptException $e) {
            throw new DecryptException("Item pakaian / penjahit / pelanggan tidak valid.");
        }
        $data['clothing_type_id'] = $clothingTypeId;
        $data['customer_id'] = $customerId;
        $data['tailor_id'] = 1;
        return $this->measurementHistoryRepository->create($data);
    }

    public function update(int $id, array $data)
    {
		if(!is_numeric($data['clothing_type_id']))
		{
			try {
				$clothingTypeId = (int) Crypt::decryptString($data['clothing_type_id']);
			} catch (DecryptException $e) {
				throw new DecryptException("Item pakaian tidak valid.");
			}
			$data['clothing_type_id'] = $clothingTypeId;
		}
        return $this->measurementHistoryRepository->update($id, $data);
    }

    public function delete(string $hashedId)
    {
        $decryptedId = Crypt::decryptString($hashedId);

        return $this->measurementHistoryRepository->delete((int) $decryptedId);
    }
}