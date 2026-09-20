<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tailor>
 */
class TailorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = \App\Models\Tailor::class;

    public function definition(): array
    {
        $faker = \Faker\Factory::create('id_ID');
        return [
            'code' => 'TLR-' . $faker->unique()->numerify('#####'),
            'name' => $faker->name(),
            'email' => $faker->unique()->safeEmail,
            'phone' => '08' . $faker->numerify('##########'),
            'address' => $faker->address(),
            'is_active' => $faker->boolean(80), 
            'specialty' => json_encode([$faker->randomElement(['Kebaya', 'Jas', 'Baju Casual', 'Seragam'])]),
        ];
    }
}
