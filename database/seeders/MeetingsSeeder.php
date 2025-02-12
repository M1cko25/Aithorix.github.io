<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Meetings;

class MeetingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Meetings::create([
            'project_id' => 1,
            'title' => 'Meeting 1',
            'description' => 'This is the first meeting',
            'date' => '2023-04-10',
            'start_time' => '2024-1-1 10:00:00',
            'end_time' => '2024-1-1 11:00:00',
            'status' => 'completed',
            'link' => 'https://meet.google.com/abc-123',
            'creator_id' => 3,
        ]);
    }
}
