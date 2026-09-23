<?php

namespace App\Services;

use App\Repositories\PaymentRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PaymentService
{
    protected PaymentRepository $paymentRepository;

    public function __construct(PaymentRepository $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }

    public function getAll(
        int $perPage = 10,
        mixed $month = null,
        mixed $year = null,
        ?string $search = null
    ): LengthAwarePaginator {
        return $this->paymentRepository->getAll($perPage, $month, $year, $search);
    }

    public function getByUlid(string $ulid)
    {
        return $this->paymentRepository->getByUlid($ulid);
    }

    public function create(array $data)
    {
        return $this->paymentRepository->create($data);
    }

    public function update(string $ulid, array $data)
    {
        return $this->paymentRepository->update($ulid, $data);
    }

    public function delete(string $ulid)
    {
        return $this->paymentRepository->delete($ulid);
    }
}