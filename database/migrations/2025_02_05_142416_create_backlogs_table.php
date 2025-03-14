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
        Schema::create('backlogs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('key');
            $table->string('description')->nullable();
            $table->string('type')->default('task');
            $table->string('status')->default('to-do');
            $table->string('priority')->default('low');
            $table->unsignedBigInteger('project_id');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->unsignedBigInteger('creator_id');
            $table->foreign('creator_id')->references('id')->on('project_members')->onDelete('cascade');
            $table->unsignedBigInteger('epic_id');
            $table->foreign('epic_id')->references('id')->on('epics')->onDelete('cascade');
            $table->unsignedBigInteger('order')->nullable();
            $table->timestamps();

            $table->unique(['project_id', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backlogs');
    }
};
