<?php

namespace App\Services;

use App\Repositories\PaymentRepository;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

class PaymentService
{
    private $paymentRepository;

    public function __construct(PaymentRepository $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }

    public function getAll(array $fields)
    {
        return $this->paymentRepository->getAll($fields);
    }

    public function getByHashedId(string $hashedId, array $fields)
    {
        try {
            $decryptedId = Crypt::decryptString($hashedId);
            return $this->paymentRepository->getById((int) $decryptedId, $fields ?? ['*']);
        } catch (DecryptException $e) {
            throw new \InvalidArgumentException("ID tidak valid.");
        }
    }

    public function create(array $data)
    {
        return $this->paymentRepository->create($data);
    }

    public function update(int $id, array $data)
    {
        return $this->paymentRepository->update($id, $data);
    }

    public function delete(string $hashedId)
    {
        $decryptedId = Crypt::decryptString($hashedId);

        return $this->paymentRepository->delete((int) $decryptedId);
    }
}