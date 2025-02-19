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
        Epic::factory(3)->create();
    }
}
