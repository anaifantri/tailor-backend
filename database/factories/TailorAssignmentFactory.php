<?php

namespace Database\Factories;

use App\Models\OrderDetail;
use App\Models\Tailor;
use App\Models\TailorAssignment;
use Illuminate\Database\Eloquent\Factories\Factory;

class TailorAssignmentFactory extends Factory
{
    protected $model = TailorAssignment::class;

    public function definition(): array
    {
        $quantity = $this->faker->numberBetween(1, 3);
        $laborCost = $this->faker->numberBetween(50, 200) * 1000;

        return [
            'order_detail_id'   => OrderDetail::factory(),
            'tailor_id'         => Tailor::factory(),
            'assignment_date'   => $this->faker->dateTimeBetween('-1 months', 'now')->format('Y-m-d'),
            'quantity_assigned' => $quantity,
            'labor_cost'        => $laborCost,
            'total_labor_cost'  => $quantity * $laborCost,
            'status'            => $this->faker->randomElement(['assigned', 'in_progress', 'completed', 'cancelled']),
            'notes'             => $this->faker->optional(0.5)->sentence(),
        ];
    }
}
