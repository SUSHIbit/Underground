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
        // Create new table for tournament judge relationships
        Schema::create('tournament_judge_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('role')->nullable()->comment('Specific role for this tournament, if any');
            $table->timestamps();
            
            // Each user can only judge a tournament once
            $table->unique(['tournament_id', 'user_id']);
        });
        
        // Drop the old tournament_judges table
        Schema::dropIfExists('tournament_judges');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the original tournament_judges table
        Schema::create('tournament_judges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('role')->nullable();
            $table->timestamps();
        });
        
        // Drop the new table
        Schema::dropIfExists('tournament_judge_users');
    }
};