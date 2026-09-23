<?php

namespace App\Repositories;

use App\Models\Payment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PaymentRepository
{
    public function getAll(
        int $perPage = 10,
        mixed $month = null,
        mixed $year = null,
        ?string $search = null
    ): LengthAwarePaginator {
        return Payment::query()
            ->byMonthYear($month, $year)
            ->search($search)
            ->with(['order.customer', 'order.payments', 'user'])
            ->latest()
            ->paginate($perPage);
    }

    public function getByUlid(string $ulid): Payment
    {
        return Payment::with(['order.customer', 'order.payments', 'user'])
            ->where('ulid', $ulid)
            ->firstOrFail();
    }

    public function create(array $data): Payment
    {
        return Payment::create($data);
    }

    public function update(string $ulid, array $data): Payment
    {
        $payment = $this->getByUlid($ulid);
        $payment->update($data);

        return $payment->fresh(['order.customer', 'order.payments', 'user']);
    }

    public function delete(string $ulid): bool
    {
        $payment = $this->getByUlid($ulid);
        return $payment->delete();
    }
}