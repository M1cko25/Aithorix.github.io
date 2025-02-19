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
        Schema::create('a_i_prompts_histories', function (Blueprint $table) {
            $table->id();
            $table->string('project_id');
            $table->foreign('project_id')->references('project_id')->on('projects')->onDelete('cascade');
            $table->string('user_id');
            $table->foreign('user_id')->references('user_id')->on('project_members')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('a_i_prompts_histories');
    }
};
