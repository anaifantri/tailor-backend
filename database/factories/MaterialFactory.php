<?php

namespace Database\Factories;

use App\Models\Material;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Material>
 */
class MaterialFactory extends Factory
{
    protected $model = Material::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ulid'          => (string) Str::ulid(), // Opsional: trait HasUlids juga menanganinya secara otomatis jika dikosongkan
            'code'          => 'MAT-' . $this->faker->unique()->numerify('#####'),
            'name'          => ucwords($this->faker->words(3, true)),
            'description'   => $this->faker->sentence(), 
            'unit'          => $this->faker->randomElement(['meter', 'yard', 'roll']),
            'initial_stock' => $this->faker->numberBetween(10, 99), 
            'stock'         => $this->faker->numberBetween(10, 99),
        ];
    }
}