<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MeetingParticipants>
 */
class MeetingParticipantsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'meeting_id' => $this->faker->numberBetween(1, 10),
            'user_id' => $this->faker->numberBetween(1, 3),
            'status' => $this->faker->randomElement(['on time', 'late', 'absent']),
        ];
    }
}
