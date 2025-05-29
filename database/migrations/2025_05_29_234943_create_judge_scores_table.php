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
        Schema::create('judge_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_participant_id')->constrained()->onDelete('cascade');
            $table->foreignId('judge_user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('score', 3, 1); // Score out of 10 with 1 decimal place
            $table->text('feedback')->nullable(); // Made nullable - judges don't have to provide feedback
            $table->json('rubric_scores')->nullable(); // Store individual rubric scores
            $table->timestamps();
            
            // Ensure each judge can only score each participant once
            $table->unique(['tournament_participant_id', 'judge_user_id'], 'judge_participant_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('judge_scores');
    }
};