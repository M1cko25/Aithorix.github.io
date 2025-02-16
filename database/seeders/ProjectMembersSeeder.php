<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProjectMembers;

class ProjectMembersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProjectMembers::factory()->create([
            'project_id' => 3,
            'user_id' => 3,
            'role' => 'Scrum Master',
        ]);
    }
}
