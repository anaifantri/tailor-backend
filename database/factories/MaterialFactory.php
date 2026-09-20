<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Material>
 */
class MaterialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    
    protected $model = \App\Models\Material::class;

    public function definition(): array
    {
        $faker = \Faker\Factory::create('id_ID');
        return [
			'code'          => $faker->unique()->numerify('##########'),
			'name'          => $faker->words(3, true),
			'description'   => $faker->sentence(), 
			'unit'          => $faker->randomElement(['meter', 'yard', 'roll']),
			'initial_stock' => $faker->numberBetween(10, 99), 
			'stock'         => $faker->numberBetween(10, 99),
        ];
    }
}
