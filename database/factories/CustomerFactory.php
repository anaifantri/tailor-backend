<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
        return [
            'ulid' => (string) Str::ulid(), // Opsional: trait HasUlids juga akan menanganinya jika dikosongkan
            'code' => 'CUST-' . $this->faker->unique()->numerify('#####'),
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => '08' . $this->faker->numerify('##########'),
            'address' => $this->faker->address(),
        ];
    }
}