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
        // Drop the existing table if it was partially created
        Schema::dropIfExists('rubric_scores');
        
        // Create the table with a shorter constraint name
        Schema::create('rubric_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_participant_id')->constrained()->onDelete('cascade');
            $table->foreignId('tournament_rubric_id')->constrained()->onDelete('cascade');
            $table->decimal('score', 3, 1); // Allows scores like 8.5
            $table->timestamps();
            
            // Use a shorter name for the unique constraint
            $table->unique(
                ['tournament_participant_id', 'tournament_rubric_id'], 
                'rubric_score_participant_rubric_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rubric_scores');
    }
};