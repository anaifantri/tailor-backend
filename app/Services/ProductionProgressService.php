<?php

namespace App\Services;

use App\Repositories\ProductionProgressRepository;

class ProductionProgressService
{
    protected ProductionProgressRepository $repository;

    public function __construct(ProductionProgressRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getByOrderDetailUlid(string $orderDetailUlid)
    {
        return $this->repository->getByOrderDetailUlid($orderDetailUlid);
    }

    public function getByUlid(string $ulid)
    {
        return $this->repository->getByUlid($ulid);
    }

    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    public function update(string $ulid, array $data)
    {
        return $this->repository->update($ulid, $data);
    }

    public function delete(string $ulid)
    {
        return $this->repository->delete($ulid);
    }
}