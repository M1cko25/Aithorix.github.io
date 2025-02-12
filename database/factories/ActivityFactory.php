<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Activity>
 */
class ActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'description' => $this->faker->sentence,
            'project_id' => 1,
            'user_id' => $this->faker->numberBetween(1, 3),
            'date' => $this->faker->dateTimeBetween('-3 day', 'now'),
            'update' => 'To Do',
        ];
    }
}
