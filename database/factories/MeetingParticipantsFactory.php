<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ProjectMembers;
use App\Models\Meetings;

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
        $projectMember = ProjectMembers::inRandomOrder()->first();

        return [
            // 'meeting_id' => 1,
            // 'user_id' => 3,
            // 'status' => $this->faker->randomElement(['on time', 'late', 'absent']),
        ]; 
    }
}
