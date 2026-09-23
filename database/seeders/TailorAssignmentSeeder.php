<?php

namespace Database\Seeders;

use App\Models\OrderDetail;
use App\Models\Tailor;
use App\Models\TailorAssignment;
use Illuminate\Database\Seeder;

class TailorAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $orderDetails = OrderDetail::all();
        $tailors = Tailor::all();

        if ($orderDetails->isEmpty() || $tailors->isEmpty()) {
            return;
        }

        // Berikan penugasan penjahit ke sebagian besar OrderDetail (70% peluang)
        foreach ($orderDetails as $orderDetail) {
            if (rand(1, 100) <= 70) {
                $quantityAssigned = rand(1, max(1, $orderDetail->quantity));
                $laborCost = rand(50, 200) * 1000;

                TailorAssignment::create([
                    'order_detail_id'   => $orderDetail->id,
                    'tailor_id'         => $tailors->random()->id,
                    'assignment_date'   => $orderDetail->created_at?->format('Y-m-d') ?? date('Y-m-d'),
                    'quantity_assigned' => $quantityAssigned,
                    'labor_cost'        => $laborCost,
                    'total_labor_cost'  => $quantityAssigned * $laborCost,
                    'status'            => fake()->randomElement(['assigned', 'in_progress', 'completed']),
                    'notes'             => 'Penugasan jahit otomatis dari seeder',
                ]);
            }
        }
    }
}