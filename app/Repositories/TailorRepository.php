<?php

namespace App\Repositories;

use App\Models\Tailor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TailorRepository
{
    public function getAll(int $perPage = 10, ?string $search = null, array $fields = ['*']): LengthAwarePaginator
    {
        return Tailor::select($fields)->search($search)->latest()->paginate($perPage);
    }

    public function getLatestByCode(): ?Tailor
    {
        return Tailor::orderBy('code', 'desc')->latest()->first();
    }

    public function getByUlid(string $ulid, array $fields = ['*']): Tailor
    {
        return Tailor::select($fields)->where('ulid', $ulid)->firstOrFail();
    }

    public function findByUlid(string $ulid): Tailor
    {
        return Tailor::where('ulid', $ulid)->firstOrFail();
    }

    public function create(array $data): Tailor
    {
        return Tailor::create($data);
    }

    public function update(string $ulid, array $data): Tailor
    {
        $tailor = $this->findByUlid($ulid);
        $tailor->update($data);

        return $tailor;
    }

    public function delete(string $ulid): void
    {
        $tailor = $this->findByUlid($ulid);
        $tailor->delete();
    }
}