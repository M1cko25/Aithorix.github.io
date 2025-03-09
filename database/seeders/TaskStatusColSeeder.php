<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TaskStatusCol;
class TaskStatusColSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TaskStatusCol::create([
            'title' => 'To Do',
            'project_id' => 1
        ]);
        TaskStatusCol::create([
            'title' => 'In Progress',
            'project_id' => 1
        ]);
        TaskStatusCol::create([
            'title' => 'Done',
            'project_id' => 1
        ]);
    }
}
