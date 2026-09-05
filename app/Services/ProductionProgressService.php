<?php

namespace App\Services;

use App\Repositories\ProductionProgressRepository;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

class ProductionProgressService
{
    private $productionProgressRepository;

    public function __construct(ProductionProgressRepository $productionProgressRepository)
    {
        $this->productionProgressRepository = $productionProgressRepository;
    }

    public function getByOrderDetailAndTailor(int $orderDetailId, int $tailorId)
    {
        $productionProgress = $this->productionProgressRepository->getByOrderDetailAndTailor($orderDetailId, $tailorId);
            
        return $productionProgress;
    }

    public function getByHashedId(string $hashedId, array $fields)
    {
        try {
            $decryptedId = Crypt::decryptString($hashedId);
            return $this->productionProgressRepository->getById((int) $decryptedId, $fields ?? ['*']);
        } catch (DecryptException $e) {
            throw new \InvalidArgumentException("ID tidak valid.");
        }
    }

    public function create(array $data)
    {
        try {
            $orderDetailId = (int) Crypt::decryptString($data['order_detail_id']);
            $tailorId = (int) Crypt::decryptString($data['tailor_id']);
        } catch (Exception $e) {
            throw new Exception("Data detail pesanan tidak valid.");
        }
        $data['order_detail_id'] = $orderDetailId;
        $data['tailor_id'] = $tailorId;
        return $this->productionProgressRepository->create($data);
    }

    public function update(int $id, array $data)
    {
        return $this->productionProgressRepository->update($id, $data);
    }

    public function delete(string $hashedId)
    {
        $decryptedId = Crypt::decryptString($hashedId);

        return $this->productionProgressRepository->delete((int) $decryptedId);
    }
}