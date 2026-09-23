<?php

namespace App\Services;

use App\Models\OrderDetailDelivery;
use App\Repositories\OrderDetailDeliveryRepository;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class OrderDetailDeliveryService
{
    public function __construct(
        protected OrderDetailDeliveryRepository $repository
    ) {}

    /**
     * Helper privat untuk mendekripsi hashed_id menjadi integer ID.
     */
    private function parseHashedId(mixed $id): ?int
    {
        if (empty($id)) {
            return null;
        }

        if (is_numeric($id)) {
            return (int) $id;
        }

        try {
            return (int) Crypt::decryptString((string) $id);
        } catch (DecryptException $e) {
            throw new InvalidArgumentException('Format ID tidak valid atau gagal didekripsi.');
        }
    }

    public function getAllDeliveries(
        int $perPage = 10,
        ?int $month = null,
        ?int $year = null,
        ?string $search = null,
        array $fields = ['*']
    ): LengthAwarePaginator {
        return $this->repository->getAll($perPage, $month, $year, $search, $fields);
    }

    public function getDeliveryById(mixed $id): OrderDetailDelivery
    {
        $realId = $this->parseHashedId($id);

        return $this->repository->findById($realId, ['order_detail.clothing_type', 'user']);
    }

    public function getDeliveriesByOrderDetail(mixed $orderDetailId): Collection
    {
        $realOrderDetailId = $this->parseHashedId($orderDetailId);

        return $this->repository->getByOrderDetail($realOrderDetailId);
    }

    public function createDelivery(array $data): OrderDetailDelivery
    {
        return DB::transaction(function () use ($data) {
            // Dekripsi order_detail_id & user_id jika dikirim sebagai hashed_id
            if (isset($data['order_detail_id'])) {
                $data['order_detail_id'] = $this->parseHashedId($data['order_detail_id']);
            }

            if (isset($data['user_id'])) {
                $data['user_id'] = $this->parseHashedId($data['user_id']);
            }

            return $this->repository->create($data);
        });
    }

    public function updateDelivery(mixed $id, array $data): OrderDetailDelivery
    {
        $realId = $this->parseHashedId($id);

        return DB::transaction(function () use ($realId, $data) {
            if (isset($data['order_detail_id'])) {
                $data['order_detail_id'] = $this->parseHashedId($data['order_detail_id']);
            }

            if (isset($data['user_id'])) {
                $data['user_id'] = $this->parseHashedId($data['user_id']);
            }

            return $this->repository->update($realId, $data);
        });
    }

    public function deleteDelivery(mixed $id): bool
    {
        $realId = $this->parseHashedId($id);

        return DB::transaction(function () use ($realId) {
            return $this->repository->delete($realId);
        });
    }
}