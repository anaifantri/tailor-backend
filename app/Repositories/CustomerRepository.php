<?php

namespace App\Repositories;

use App\Models\Customer;

class CustomerRepository
{
    public function getAll(int $perPage = 10, ?string $search = null, array $fields){
        return Customer::select($fields)->search($search)->latest()->paginate($perPage);
    }

    public function getLatestByCode()
    {
        return Customer::orderBy('code', 'desc')->latest()->first();
    }

    public function getById(int $id, array $fields){
        return Customer::select($fields)->with(['measurement_histories', 'measurement_histories.clothing_type'])->findOrFail($id);
    }

    public function create(array $data){
        return Customer::create($data);
    }

    public function update(int $id, array $data){
        $customer = Customer::findOrFail($id);

        $customer->update($data);

        return $customer;
    }

    public function delete(int $id){
        $customer = Customer::findOrFail($id);

        $customer->delete();
    }
}