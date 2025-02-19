<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Epic>
 */
class EpicFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => 1,
            'name' => $this->faker->name(),
            'description' => $this->faker->text(),
            'start_date' => now(),
            'end_date' => $this->faker->dateTimeBetween('now', '+7 day'),
            'progress_precent' => $this->faker->numberBetween(0, 100),
            'key' => 'AITH-E' . $this->faker->unique()->numberBetween(1, 3),
            'order' => $this->faker->numberBetween(0, 3),
        ];
    }
}
