<?php

namespace App\Repositories;

use App\Models\Order;

class OrderRepository
{
    public function getAll(int $month, int $year, ?string $search = null, array $fields)
    {
        return Order::select($fields)->byMonthYear($month, $year)->search($search)->with(['order_details','order_details.clothing_type','order_details.measurement_history', 'order_details.production_progress','order_details.material','payments', 'customer'])->latest()->paginate(10);
    }

    public function getUnpaid(?string $search = null, array $fields)
    {
        return Order::select($fields)->search($search)->unpaid()->with(['order_details','order_details.measurement_history','order_details.clothing_type', 'order_details.production_progress','order_details.material','payments', 'customer'])->latest()->get();
    }

    public function getLatestByNumber()
    {
        return Order::orderBy('number', 'desc')->latest()->first();
    }

    public function getById(int $id, array $fields)
    {
        return Order::select($fields)->with(['order_details','order_details.clothing_type','order_details.measurement_history', 'order_details.production_progress','order_details.material','payments', 'customer'])->findOrFail($id);
    }

    public function findById(int $id): ?Order
    {
        return Order::find($id); 
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