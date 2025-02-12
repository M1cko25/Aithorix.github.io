<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Meetings>
 */
class MeetingsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'link' => $this->faker->url,
            'status' => $this->faker->randomElement(['pending', 'completed']),
            'date' => $this->faker->date,
            'start_time' => $this->faker->dateTimeBetween('now', '+1 week'),
            'end_time' => $this->faker->dateTimeBetween('+1 week', '+2 weeks'),
            'project_id' => 1,
            'creator_id' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
