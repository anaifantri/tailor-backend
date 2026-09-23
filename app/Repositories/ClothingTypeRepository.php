<?php

namespace App\Repositories;

use App\Models\ClothingType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ClothingTypeRepository
{
    public function getAll(int $perPage = 10, ?string $search = null, array $fields = ['*']): LengthAwarePaginator
    {
        return ClothingType::select($fields)->search($search)->latest()->paginate($perPage);
    }

    public function getByUlid(string $ulid, array $fields = ['*']): ClothingType
    {
        return ClothingType::select($fields)->where('ulid', $ulid)->firstOrFail();
    }

    public function findByUlid(string $ulid): ClothingType
    {
        return ClothingType::where('ulid', $ulid)->firstOrFail();
    }

    public function create(array $data): ClothingType
    {
        return ClothingType::create($data);
    }

    public function update(string $ulid, array $data): ClothingType
    {
        $clothingType = $this->findByUlid($ulid);
        $clothingType->update($data);

        return $clothingType;
    }

    public function delete(string $ulid): void
    {
        $clothingType = $this->findByUlid($ulid);
        $clothingType->delete();
    }
}