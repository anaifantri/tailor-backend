<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = \Faker\Factory::create('id_ID'); 
        return [
            'code' => 'CUST-' . $faker->unique()->numerify('#####'),
            'name' => $faker->name,
            'email' => $faker->unique()->safeEmail,
            'phone' => '08' . $faker->numerify('##########'),
            'address' => $faker->address,
        ];
    }
}
