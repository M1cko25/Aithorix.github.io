<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Epic;

class EpicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Epic::create(
        //     [
        //     'project_id' => 1,
        //     'name' => 'Epic 1',
        //     'description' => 'Epic 1 description',
        //     'progress_precent' => 0,
        //     'key' => 'AITH-E1',
        //     'order' => 1,
        //     'created_at' => now(),
        //     'updated_at' => now(),
        // ], 
        [
            'project_id' => 1,
            'name' => 'Epic 2',
            'description' => 'Epic 2 description',
            'progress_precent' => 0,
            'key' => 'AITH-E2',
            'order' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
