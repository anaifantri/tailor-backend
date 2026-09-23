<?php

namespace App\Services;

use App\Models\Customer;
use App\Repositories\CustomerRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CustomerService
{
    private CustomerRepository $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function getAll(int $perPage = 10, ?string $search = null, array $fields = ['*']): LengthAwarePaginator
    {
        return $this->customerRepository->getAll($perPage, $search, $fields);
    }

    public function getByUlid(string $ulid, array $fields = ['*']): Customer
    {
        return $this->customerRepository->getByUlid($ulid, $fields);
    }

    public function findByUlid(string $ulid): Customer
    {
        return $this->customerRepository->findByUlid($ulid);
    }

    public function create(array $data): Customer
    {
        $lastCustomer = $this->customerRepository->getLatestByCode();
        if (!$lastCustomer) {
            $number = 1;
        } else {
            $number = (int) substr($lastCustomer->code, 5) + 1;
        }
        
        $data['code'] = 'CUST-' . str_pad((string) $number, 5, '0', STR_PAD_LEFT);

        return $this->customerRepository->create($data);
    }

    public function update(string $ulid, array $data): Customer
    {
        return $this->customerRepository->update($ulid, $data);
    }

    public function delete(string $ulid): void
    {
        $this->customerRepository->delete($ulid);
    }
}