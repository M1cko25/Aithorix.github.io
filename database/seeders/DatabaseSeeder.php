<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(20)->create();
        $this->call([
        // ProjectSeeder::class,
        // ActivitySeeder::class,
        EpicSeeder::class,
        
        // BacklogsSeeder::class,
        // MeetingsSeeder::class,
        // SprintSeeder::class,
        // MeetingParticipantsSeeder::class,
        
    ]);
    }
}
