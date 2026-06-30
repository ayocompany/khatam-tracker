<?php

namespace Database\Factories;

use App\Models\QuranLayout;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QuranLayout>
 */
class QuranLayoutFactory extends Factory
{
    protected $model = QuranLayout::class;

    public function definition(): array
    {
        return [
            'code'           => $this->faker->unique()->bothify('layout_??'),
            'name'           => $this->faker->words(3, true),
            'total_pages'    => 604,
            'total_surahs'   => 114,
            'total_verses'   => 6236,
            'lines_per_page' => 15,
            'is_active'      => true,
        ];
    }
}
