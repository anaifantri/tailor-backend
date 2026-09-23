<?php

namespace Database\Factories;

use App\Models\ClothingType;
use App\Models\Customer;
use App\Models\MeasurementHistory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MeasurementHistoryFactory extends Factory
{
    protected $model = MeasurementHistory::class;

    public function definition(): array
    {
        // 1. Ambil atau buat instance Customer dan ClothingType secara dinamis
        $customer = Customer::inRandomOrder()->first() ?? Customer::factory()->create();
        $clothingType = ClothingType::inRandomOrder()->first() ?? ClothingType::factory()->create();

        $category = strtolower($clothingType->category ?? 'baju');

        // 2. Format detail ukuran berupa array PHP (Laravel Eloquent Cast akan mengubahnya ke JSON secara otomatis)
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
            'ulid' => (string) Str::ulid(),
            'customer_id' => $customer->id,
            'customer_ulid' => $customer->ulid,
            'clothing_type_id' => $clothingType->id,
            'clothing_type_ulid' => $clothingType->ulid,
            'category' => $clothingType->category ?? 'Baju',
            'measured_by' => $this->faker->name(),
            'measured_at' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'measurement_details' => $measurementDetails,
            'notes' => $this->faker->optional(0.7)->sentence(),
        ];
    }
}