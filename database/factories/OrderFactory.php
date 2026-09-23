<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $orderDate = $this->faker->dateTimeBetween('-1 months', 'now');
        $fittingDate = (clone $orderDate)->modify('+7 days');
        $dueDate = (clone $orderDate)->modify('+14 days');

        return [
            'number' => 'ORD-' . date('Ymd') . '-' . $this->faker->unique()->numerify('####'),
            'user_id' => User::factory(),
            'customer_id' => Customer::factory(),
            'order_date' => $orderDate->format('Y-m-d'),
            'fitting_date' => $fittingDate->format('Y-m-d'),
            'due_date' => $dueDate->format('Y-m-d'),
            'discount' => $this->faker->randomElement([0, 25000, 50000]),
            'tax' => 0,
            'total' => 0, // Nilai awal, nanti akan dihitung ulang via Seeder berdasarkan detail item
            'notes' => $this->faker->optional(0.6)->sentence(),
        ];
    }
}