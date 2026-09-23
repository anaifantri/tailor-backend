<?php

namespace Database\Factories;

use App\Models\ClothingType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClothingType>
 */
class ClothingTypeFactory extends Factory
{
    protected $model = ClothingType::class;

    public function definition(): array
    {
        // Pemetaan pilihan type berdasarkan kategorinya
        $typesByCategory = [
            'baju' => [
                'Kemeja Lengan Panjang',
                'Kemeja Lengan Pendek',
                'Jas Formal',
                'PDH harian',
                'PDL Lapangan',
                'Jaket Bomber',
                'Kaos Polos Premium',
                'Blazer Wanita',
            ],
            'celana' => [
                'Celana Panjang Formal',
                'Celana Chino Slim',
                'Celana Cargo Tactical',
                'Celana Jeans Standard',
                'Celana Pendek Casual',
            ],
            'rok' => [
                'Rok Plisket',
                'Rok Span Kerja',
                'Rok A-Line Modern',
                'Rok Rempel Sekolah',
                'Rok Payung Lebar',
            ],
        ];

        // Tentukan kategori secara acak terlebih dahulu
        $category = $this->faker->randomElement(array_keys($typesByCategory));

        // Ambil type acak secara unik sesuai kategori yang terpilih
        $type = $this->faker->unique()->randomElement($typesByCategory[$category]);

        return [
            'ulid'       => (string) Str::ulid(), // Opsional: Trait HasUlids juga menanganinya secara otomatis jika dikosongkan
            'code'       => 'MDL-' . $this->faker->unique()->numerify('####'),
            'type'       => $type,
            'category'   => $category,
            'base_price' => $this->faker->numberBetween(50, 750) * 1000,
        ];
    }
}