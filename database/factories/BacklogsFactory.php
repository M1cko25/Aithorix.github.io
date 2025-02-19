<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Backlogs>
 */
class BacklogsFactory extends Factory
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
            'type' => $this->faker->randomElement(['task', 'bug', 'feature']),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high']),
            'epic_id' => 1,
            'project_id' => 1,
            'creator_id' => 3,
            'status' => $this->faker->randomElement(['to do', 'in_progress', 'completed']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
