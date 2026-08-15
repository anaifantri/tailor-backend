<?php

namespace App\Repositories;

use App\Models\Order;

class OrderRepository
{
    public function getAll(array $fields)
    {
        return Order::select($fields)->latest()->paginate(10);
    }

    public function getLatestByNumber()
    {
        return Order::orderBy('number', 'desc')->latest()->first();
    }

    public function getById(int $id, array $fields)
    {
        return Order::select($fields)->with('client')->with('user')->findOrFail($id);
    }

    public function create(array $data)
    {
        return Order::create($data);
    }

    public function update(int $id, array $data)
    {
        $order = Order::findOrFail($id);

        $order->update($data);

        return $order;
    }

    public function delete(int $id)
    {
        $order = Order::findOrFail($id);

        $order->delete();
    }
}