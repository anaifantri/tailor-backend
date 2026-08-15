<?php

namespace App\Repositories;

use App\Models\Payment;

class PaymentRepository
{
    public function getAll(array $fields){
        return Payment::select($fields)->latest()->paginate(10);
    }

    public function getById(int $id, array $fields){
        return Payment::select($fields)->findOrFail($id);
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