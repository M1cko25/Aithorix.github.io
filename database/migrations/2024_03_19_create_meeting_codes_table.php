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
        Schema::create('meeting_codes', function (Blueprint $table) {
            $table->id();
            $table->string('meeting_name');
            $table->string('code', 9)->unique();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamp('expires_at');
            $table->foreignId('project_id')->constrained('projects');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meeting_codes');
    }
}; 