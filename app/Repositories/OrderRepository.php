<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderRepository
{
    private array $defaultWith = [
        'customer',
        'user',
        'orderDetails.clothingType',
        'orderDetails.material',
        'orderDetails.productionProgress',
        'payments'
    ];

    public function getAll(int $perPage = 10, mixed $month = null, ?int $year = null, ?string $search = null): LengthAwarePaginator
    {
        return Order::with($this->defaultWith)
            ->byMonthYear($month, $year)
            ->search($search)
            ->latest()
            ->paginate($perPage);
    }

    public function getUnpaid(?string $search = null)
    {
        return Order::with($this->defaultWith)
            ->search($search)
            ->unpaid()
            ->latest()
            ->get();
    }

    public function getBySearch(?string $search = null)
    {
        return Order::with($this->defaultWith)
            ->search($search)
            ->latest()
            ->get();
    }

    public function getByUlid(string $ulid): Order
    {
        return Order::with($this->defaultWith)
            ->where('ulid', $ulid)
            ->firstOrFail();
    }

    public function create(array $data): Order
    {
        return Order::create($data);
    }

    public function update(string $ulid, array $data): Order
    {
        $order = $this->getByUlid($ulid);
        $order->update($data);

        return $order->fresh($this->defaultWith);
    }

    public function delete(string $ulid): bool
    {
        $order = $this->getByUlid($ulid);
        return $order->delete();
    }
}