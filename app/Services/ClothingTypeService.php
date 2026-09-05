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

    public function getAll(?string $search = null, array $fields)
    {
        return $this->clothingTypeRepository->getAll($search, $fields);
    }

    public function getByHashedId(string $hashedId, array $fields)
    {
        try {
            $decryptedId = Crypt::decryptString($hashedId);
            return $this->clothingTypeRepository->getById((int) $decryptedId, $fields ?? ['*']);
        } catch (DecryptException $e) {
            throw new \InvalidArgumentException("ID tidak valid.");
        }
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) { 
            $clothingType = $this->clothingTypeRepository->create($data);
            
            if (!empty($data['measurements'])) {
                $clothingType->measurements()->createMany($data['measurements']);
            }
                
            return $clothingType->load('measurements');
        });
    }

    public function update(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $clothingType = $this->clothingTypeRepository->update($id, $data);
            
            if (!empty($data['measurements'])) {
                $clothingType->measurements()->delete();

                $clothingType->measurements()->createMany($data['measurements']);
            }
            return $clothingType->load('measurements');
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