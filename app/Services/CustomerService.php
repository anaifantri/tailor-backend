<?php

namespace App\Services;

use App\Repositories\CustomerRepository;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

class CustomerService
{
    private $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function getAll(int $perPage = 10, ?string $search = null, array $fields)
    {
        return $this->customerRepository->getAll($perPage, $search, $fields);
    }

    public function getByHashedId(string $hashedId, array $fields)
    {
        try {
            $decryptedId = Crypt::decryptString($hashedId);
        } catch (DecryptException $e) {
            throw new \InvalidArgumentException("ID tidak valid.");
        }
            return $this->customerRepository->getById((int) $decryptedId, $fields ?? ['*']);
    }

    public function create(array $data)
    {
        $fields = ['id', 'code'];
        $lastCustomer = $this->customerRepository->getLatestByCode();
        if(!$lastCustomer){
            $number = 1;
        }else{
            $number = (int) substr($lastCustomer->code, 5) + 1;
        }
        $newCustomerCode = 'CUST-' . str_pad($number, 5, '0', STR_PAD_LEFT);

        $data['code'] = $newCustomerCode;

        return $this->customerRepository->create($data);
    }

    public function update(int $id, array $data)
    {
        $fields = ['id'];

        return $this->customerRepository->update($id, $data);
    }

    public function delete(string $hashedId)
    {
        $fields = ['id'];
        $decryptedId = Crypt::decryptString($hashedId);

        return $this->customerRepository->delete((int) $decryptedId);
    }
}