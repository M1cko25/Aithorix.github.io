<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Backlogs;

class BacklogsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Backlogs::factory(10)->create();
        Backlogs::create([
            'title' => 'Backlog 1',
            'description' => 'This is the first backlog',
            'project_id' => 1,
            'creator_id' => 3,
            'type' => 'Task',
            'epic_id' => 1,
            'status' => 'to do',
            'order' => 1,
        ]);
    }
}
