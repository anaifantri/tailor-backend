<?php

namespace Database\Factories;

use App\Models\ClothingType;
use App\Models\Customer;
use App\Models\MeasurementHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

class MeasurementHistoryFactory extends Factory
{
    protected $model = MeasurementHistory::class;

    public function definition(): array
    {
        $clothingType = ClothingType::inRandomOrder()->first() ?? ClothingType::factory()->create();
        // $category = $this->faker->randomElement(['baju', 'celana', 'rok']);
        $category = strtolower($clothingType->category);

        // 2. Format detail ukuran menjadi array objek dengan key 'name' dan 'value'
        $measurementDetails = match ($category) {
            'baju' => [
                ['name' => 'Panjang Badan', 'value' => $this->faker->numberBetween(40, 52)],
                ['name' => 'Lebar Bahu', 'value' => $this->faker->numberBetween(40, 52)],
                ['name' => 'Panjang Tangan', 'value' => $this->faker->numberBetween(40, 52)],
                ['name' => 'Lingkar Lengan', 'value' => $this->faker->numberBetween(40, 52)],
                ['name' => 'Manset', 'value' => $this->faker->numberBetween(40, 52)],
                ['name' => 'Lingkar Badan', 'value' => $this->faker->numberBetween(85, 115)],
                ['name' => 'Lingkar Perut', 'value' => $this->faker->numberBetween(85, 115)],
                ['name' => 'Lingkar Pinggul', 'value' => $this->faker->numberBetween(85, 115)],
                ['name' => 'Lebar Dada', 'value' => $this->faker->numberBetween(50, 65)],
                ['name' => 'Lebar Punggung', 'value' => $this->faker->numberBetween(65, 80)],
                ['name' => 'Lingkar Leher', 'value' => $this->faker->numberBetween(65, 80)],
            ],
            'celana' => [
                ['name' => 'Panjang Celana', 'value' => $this->faker->numberBetween(70, 100)],
                ['name' => 'Lingkar Pinggang', 'value' => $this->faker->numberBetween(70, 100)],
                ['name' => 'Lingkar Pinggul', 'value' => $this->faker->numberBetween(90, 110)],
                ['name' => 'Pesak', 'value' => $this->faker->numberBetween(90, 105)],
                ['name' => 'Lingkar Paha', 'value' => $this->faker->numberBetween(50, 65)],
                ['name' => 'Lingkar Lutut', 'value' => $this->faker->numberBetween(50, 65)],
                ['name' => 'Lingkar Kaki', 'value' => $this->faker->numberBetween(50, 65)],
            ],
            'rok' => [
                ['name' => 'Panjang Rok', 'value' => $this->faker->numberBetween(40, 95)],
                ['name' => 'Lingkar Pinggang', 'value' => $this->faker->numberBetween(60, 85)],
                ['name' => 'Lingkar Pinggul', 'value' => $this->faker->numberBetween(85, 105)],
            ],
            default => [],
        };

        return [
            'customer_id' => Customer::inRandomOrder()->first()?->id ?? Customer::factory(),
            'clothing_type_id' => $clothingType->id,
            'category' => $clothingType->category,
            'measured_by' => $this->faker->name(),
            'measured_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            
            'measurement_details' => json_encode($measurementDetails), // Menyimpan array objek [ {name, value}, ... ]
            
            'notes' => $this->faker->optional(0.7)->sentence(),
        ];
    }
}
