<?php

namespace App\Services;

use App\Models\TailorAssignment;
use App\Repositories\TailorAssignmentRepository;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class TailorAssignmentService
{
    public function __construct(
        protected TailorAssignmentRepository $repository
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

    public function getAllAssignments(
        int $perPage = 10,
        ?int $month = null,
        ?int $year = null,
        ?string $search = null,
        array $fields = ['*']
    ): LengthAwarePaginator {
        return $this->repository->getAll($perPage, $month, $year, $search, $fields);
    }

    public function getAssignmentById(mixed $id): TailorAssignment
    {
        $realId = $this->parseHashedId($id);

        return $this->repository->findById($realId, ['order_detail.clothing_type', 'tailor']);
    }

    public function getAssignmentsByOrderDetail(mixed $orderDetailId): Collection
    {
        $realOrderDetailId = $this->parseHashedId($orderDetailId);

        return $this->repository->getByOrderDetail($realOrderDetailId);
    }

    public function createAssignment(array $data): TailorAssignment
    {
        return DB::transaction(function () use ($data) {
            // Dekripsi order_detail_id & tailor_id jika dikirim sebagai hashed_id
            if (isset($data['order_detail_id'])) {
                $data['order_detail_id'] = $this->parseHashedId($data['order_detail_id']);
            }

            if (isset($data['tailor_id'])) {
                $data['tailor_id'] = $this->parseHashedId($data['tailor_id']);
            }

            // Kalkulasi otomatis total_labor_cost jika belum dihitung
            if (!isset($data['total_labor_cost']) && isset($data['quantity_assigned'], $data['labor_cost'])) {
                $data['total_labor_cost'] = (int) $data['quantity_assigned'] * (float) $data['labor_cost'];
            }

            // Status default jika kosong
            $data['status'] = $data['status'] ?? 'assigned';

            return $this->repository->create($data);
        });
    }

    public function updateAssignment(mixed $id, array $data): TailorAssignment
    {
        $realId = $this->parseHashedId($id);

        return DB::transaction(function () use ($realId, $data) {
            // Dekripsi foreign key jika dikirim dalam array payload
            // if (isset($data['order_detail_id'])) {
            //     $data['order_detail_id'] = $this->parseHashedId($data['order_detail_id']);
            // }

            // if (isset($data['tailor_id'])) {
            //     $data['tailor_id'] = $this->parseHashedId($data['tailor_id']);
            // }

            // Kalkulasi ulang total_labor_cost jika ada perubahan qty atau labor_cost
            if (isset($data['quantity_assigned']) || isset($data['labor_cost'])) {
                $assignment = $this->repository->findById($realId);
                $qty = $data['quantity_assigned'] ?? $assignment->quantity_assigned;
                $cost = $data['labor_cost'] ?? $assignment->labor_cost;

                $data['total_labor_cost'] = (int) $qty * (float) $cost;
            }

            return $this->repository->update($realId, $data);
        });
    }

    public function updateAssignmentStatus(mixed $id, string $status): TailorAssignment
    {
        $realId = $this->parseHashedId($id);

        return DB::transaction(function () use ($realId, $status) {
            return $this->repository->updateStatus($realId, $status);
        });
    }

    public function deleteAssignment(mixed $id): bool
    {
        $realId = $this->parseHashedId($id);

        return DB::transaction(function () use ($realId) {
            return $this->repository->delete($realId);
        });
    }
}