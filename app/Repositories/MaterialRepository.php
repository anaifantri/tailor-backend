<?php

namespace App\Repositories;

use App\Models\Material;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MaterialRepository
{
    public function getAll(int $perPage = 10, ?string $search = null, array $fields = ['*']): LengthAwarePaginator
    {
        return Material::select($fields)->search($search)->latest()->paginate($perPage);
    }

    public function getByUlid(string $ulid, array $fields = ['*']): Material
    {
        return Material::select($fields)->where('ulid', $ulid)->firstOrFail();
    }

    public function findByUlid(string $ulid): Material
    {
        return Material::where('ulid', $ulid)->firstOrFail();
    }

    public function create(array $data): Material
    {
        return Material::create($data);
    }

    public function update(string $ulid, array $data): Material
    {
        $material = $this->findByUlid($ulid);
        $material->update($data);

        return $material;
    }

    public function delete(string $ulid): void
    {
        $material = $this->findByUlid($ulid);
        $material->delete();
    }
}