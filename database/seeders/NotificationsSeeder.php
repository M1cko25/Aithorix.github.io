<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Notifications;

class NotificationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Notifications::create([
            'title' => 'New task assigned to you',
            'content' => 'John Doe assigned a task for you',
            'sender_id' => 1,
            'receiver_id' => 3,
            'is_read' => false,
            'type' => 'task_assigned'
        ]);
    }
}
