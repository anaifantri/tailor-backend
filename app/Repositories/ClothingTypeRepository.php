<?php

namespace App\Repositories;

use App\Models\ClothingType;

class ClothingTypeRepository
{
    public function getAll(int $perPage = 10, ?string $search = null, array $fields){
        return ClothingType::select($fields)->search($search)
        ->latest()->paginate($perPage);
    }

    public function getById(int $id, array $fields)
    {
        return ClothingType::select($fields)
        ->findOrFail($id);
    }

    public function findById(int $id): ?ClothingType
    {
        return ClothingType::find($id); 
    }

    public function create(array $data)
    {
        return ClothingType::create($data);
    }

    public function update(int $id, array $data)
    {
        $clothingType = $this->findById($id);

        $clothingType->update($data);

        return $clothingType;
    }

    public function delete(int $id)
    {
        $clothingType = $this->findById($id);

        $clothingType->delete();
    }
}