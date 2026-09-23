<?php

namespace App\Services;

use App\Models\ClothingType;
use App\Repositories\ClothingTypeRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ClothingTypeService
{
    private ClothingTypeRepository $clothingTypeRepository;

    public function __construct(ClothingTypeRepository $clothingTypeRepository)
    {
        $this->clothingTypeRepository = $clothingTypeRepository;
    }

    public function getAll(int $perPage = 10, ?string $search = null, array $fields = ['*']): LengthAwarePaginator
    {
        return $this->clothingTypeRepository->getAll($perPage, $search, $fields);
    }

    public function getByUlid(string $ulid, array $fields = ['*']): ClothingType
    {
        return $this->clothingTypeRepository->getByUlid($ulid, $fields);
    }

    public function create(array $data): ClothingType
    {
        return DB::transaction(function () use ($data) { 
            return $this->clothingTypeRepository->create($data);
        });
    }

    public function update(string $ulid, array $data): ClothingType
    {
        return DB::transaction(function () use ($ulid, $data) {
            return $this->clothingTypeRepository->update($ulid, $data);
        });
    }

    public function delete(string $ulid): void
    {
        DB::transaction(function () use ($ulid) {
            $this->clothingTypeRepository->delete($ulid);
        });
    }
}