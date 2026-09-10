<?php

namespace App\Repositories;

use App\Models\Payment;

class PaymentRepository
{
    public function getAll(int $month, int $year, ?string $search = null, array $fields){
        return Payment::select($fields)->byMonthYear($month, $year)->search($search)->with(['order', 'order.customer', 'order.payments'])->latest()->paginate(10);
    }

    public function getById(int $id, array $fields){
        return Payment::select($fields)->with(['order', 'order.customer', 'order.payments', 'user'])->findOrFail($id);
    }

    public function create(array $data){
        return Payment::create($data);
    }

    public function update(int $id, array $data){
        $payment = Payment::findOrFail($id);

        $payment->update($data);

        return $payment;
    }

    public function delete(int $id){
        $payment = Payment::findOrFail($id);

        $payment->delete();
    }
}