<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClothingType>
 */
class ClothingTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = \App\Models\ClothingType::class;

    public function definition(): array
    {
        $type = fake()->unique()->randomElement([
            'Kemeja Lengan Panjang', 'Kemeja Lengan Pendek', 'Celana', 'Rok', 
            'Jas', 'PDH', 'PDL', 'Jaket Bomber', 
            'Kaos Polos', 'Rok Plisket'
        ]);

        return [
            'code'       => 'MDL-' . fake()->unique()->numerify('####'),
            'type'       => $type,
			'category'       => fake()->randomElement(['baju', 'celana', 'rok']),
            'base_price' => fake()->numberBetween(50, 750) * 1000,
        ];
    }
}
