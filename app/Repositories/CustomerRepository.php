<?php

namespace App\Repositories;

use App\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CustomerRepository
{
    public function getAll(int $perPage = 10, ?string $search = null, array $fields = ['*']): LengthAwarePaginator
    {
        return Customer::select($fields)->search($search)->latest()->paginate($perPage);
    }

    public function getLatestByCode(): ?Customer
    {
        return Customer::orderBy('code', 'desc')->latest()->first();
    }

    public function getByUlid(string $ulid, array $fields = ['*']): Customer
    {
        return Customer::select($fields)
            ->with(['measurement_histories', 'measurement_histories.clothing_type'])
            ->where('ulid', $ulid)
            ->firstOrFail();
    }

    public function findByUlid(string $ulid): Customer
    {
        return Customer::where('ulid', $ulid)->firstOrFail();
    }

    public function create(array $data): Customer
    {
        return Customer::create($data);
    }

    public function update(string $ulid, array $data): Customer
    {
        $customer = $this->findByUlid($ulid);
        $customer->update($data);

        return $customer;
    }

    public function delete(string $ulid): void
    {
        $customer = $this->findByUlid($ulid);
        $customer->delete();
    }
}