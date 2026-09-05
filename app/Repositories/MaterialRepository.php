<?php

namespace App\Repositories;

use App\Models\Material;

class MaterialRepository
{
    public function getAll(?string $search = null, array $fields){
        return Material::select($fields)->search($search)->latest()->paginate(10);
    }

    public function getById(int $id, array $fields)
    {
        return Material::select($fields)->findOrFail($id);
    }

    public function create(array $data)
    {
        return Material::create($data);
    }

    public function update(int $id, array $data)
    {
        $material = Material::findOrFail($id);

        $material->update($data);

        return $material;
    }

    public function delete(int $id)
    {
        $material = Material::findOrFail($id);

        $material->delete();
    }
}