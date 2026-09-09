<?php

namespace Database\Factories;

use App\Models\Province;
use App\Models\Ump;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ump>
 */
class UmpFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kode_provinsi' => Province::inRandomOrder()->value('kode_provinsi') ?? '31',
            'tahun' => 2026,
            'besaran' => fake()->numberBetween(2500000, 5500000),
            'catatan' => null,
        ];
    }
}
