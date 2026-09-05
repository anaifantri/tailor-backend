<?php

namespace App\Repositories;

use App\Models\ClothingType;

class ClothingTypeRepository
{
    public function getAll(?string $search = null, array $fields){
        return ClothingType::select($fields)->search($search)->latest()->paginate(10);
    }

    public function getById(int $id, array $fields)
    {
        return ClothingType::select($fields)->with('measurements')->findOrFail($id);
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
        $clothingType = ClothingType::findOrFail($id);

        $clothingType->update($data);

        return $clothingType;
    }

    public function delete(int $id)
    {
        $clothingType = ClothingType::findOrFail($id);

        $clothingType->delete();
    }
}