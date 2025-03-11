<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sprint_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sprint_id')->constrained('sprints');
            $table->foreignId('backlog_id')->constrained('backlogs');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('duration');
            $table->integer('progress');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sprint_tasks');
    }
};
