<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MeetingParticipants;

class MeetingParticipantsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $participants = [
        [
            'meeting_id' => 1,
            'user_id' => 3,
            'status' => 'on time',
        ], 
        [
            'meeting_id' => 1,
            'user_id' => 4,
            'status' => 'on time',
        ],
        [
            'meeting_id' => 1,
            'user_id' => 2,
            'status' => 'on time',
        ], [
            'meeting_id' => 1,
            'user_id' => 8,
            'status' => 'late',
        ],
        [
            'meeting_id' => 1,
            'user_id' => 1,
            'status' => 'late',
        ],[
            'meeting_id' => 1,
            'user_id' => 12,
            'status' => 'absent',
        ]
    ];
        foreach ($participants as $participant) {
            MeetingParticipants::create($participant);
        }
    }
}
