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

    public function create(array $data)
    {
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