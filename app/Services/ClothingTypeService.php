<?php

namespace App\Services;

use App\Repositories\ClothingTypeRepository;
use Exception;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class ClothingTypeService
{
    private $clothingTypeRepository;

    public function __construct(ClothingTypeRepository $clothingTypeRepository)
    {
        $this->clothingTypeRepository = $clothingTypeRepository;
    }

    public function getAll(int $perPage = 10, ?string $search = null, array $fields)
    {
        return $this->clothingTypeRepository->getAll($perPage, $search, $fields);
    }

    public function getByHashedId(string $hashedId, array $fields)
    {
        try {
            $decryptedId = Crypt::decryptString($hashedId);
        } catch (DecryptException $e) {
            throw new \InvalidArgumentException("ID tidak valid.");
        }
		
            return $this->clothingTypeRepository->getById((int) $decryptedId, $fields ?? ['*']);
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) { 
            $clothingType = $this->clothingTypeRepository->create($data);
                
            return $clothingType;
        });
    }

    public function update(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $clothingType = $this->clothingTypeRepository->update($id, $data);

            return $clothingType;
        });
    }

    public function delete(string $hashedId)
    {
        try {
            $clothingTypeId = (int) Crypt::decryptString($hashedId);
        } catch (Exception $e) {
            throw new Exception("Data jenis pakaian tidak valid.");
        }
        return DB::transaction(function () use ($clothingTypeId) {
            return $this->clothingTypeRepository->delete($clothingTypeId);
        });
    }
}