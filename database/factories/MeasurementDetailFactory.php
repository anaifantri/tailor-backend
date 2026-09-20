<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MeasurementDetail>
 */
class MeasurementDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $measurement = fake()->randomElement([
            'Lingkar Leher', 'Lingkar Perut', 'Lingkar Badan', 'Lebar Dada', 
            'Lebar Punggung', 'Panjang Tangan', 'Panjang Celana', 'Manset', 
            'Lingkar Paha', 'Lingkar Kaki'
        ]);
        return [
            'measurement' => $measurement,
        ];
    }
}
