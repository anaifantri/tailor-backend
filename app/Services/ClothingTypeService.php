<?php

namespace App\Services;

use App\Repositories\ClothingTypeRepository;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

class ClothingTypeService
{
    private $clothingTypeRepository;

    public function __construct(ClothingTypeRepository $clothingTypeRepository)
    {
        $this->clothingTypeRepository = $clothingTypeRepository;
    }

    public function getAll(array $fields)
    {
        return $this->clothingTypeRepository->getAll($fields);
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
        $clothingType = $this->clothingTypeRepository->create($data);
        
        if (!empty($data['measurements'])) {
            $clothingType->measurements()->createMany($data['measurements']);
         }
            
        return $clothingType->load('measurements');
    }

    public function update(int $id, array $data)
    {
        $fields = ['id','type'];
        $clothingType = $this->clothingTypeRepository->getById($id, $fields);

        $this->clothingTypeRepository->update($id, $data);
        
        if (!empty($data['measurements'])) {
            $clothingType->measurements()->delete();

            $clothingType->measurements()->createMany($data['measurements']);
         }
        return $clothingType->load('measurements');
    }

    public function delete(string $hashedId)
    {
        $decryptedId = Crypt::decryptString($hashedId);
        return $this->clothingTypeRepository->delete((int) $decryptedId);
    }
}