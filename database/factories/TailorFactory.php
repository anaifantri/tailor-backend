<?php

namespace Database\Factories;

use App\Models\ClothingType;
use App\Models\Tailor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tailor>
 */
class TailorFactory extends Factory
{
    protected $model = Tailor::class;

    public function definition(): array
    {
        // Ambil type yang tersedia dari database ClothingType
        $clothingType = ClothingType::inRandomOrder()->value('type');

        return [
            'ulid' => (string) Str::ulid(),
            'code' => 'TLR-' . $this->faker->unique()->numerify('#####'),
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => '08' . $this->faker->numerify('##########'),
            'address' => $this->faker->address(),
            'is_active' => $this->faker->boolean(80), 
            'specialty' => [
                // Gunakan nilai dari DB, atau fallback ke default jika DB ClothingType masih kosong
                $clothingType ?? $this->faker->randomElement(['Kebaya', 'Jas', 'Baju Casual', 'Seragam'])
            ],
        ];
    }
}