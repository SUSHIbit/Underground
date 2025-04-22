<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Create teams table
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tournament_teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->foreignId('leader_id')->constrained('users');
            $table->timestamps();
            
            // Each team name must be unique within a tournament
            $table->unique(['tournament_id', 'name']);
        });
        
        // Create team invitations table
        Schema::create('team_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('tournament_teams')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['pending', 'accepted', 'declined', 'expired'])->default('pending');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
            
            // Each user can only be invited once to a specific team
            $table->unique(['team_id', 'user_id']);
        });
        
        // Modify the tournament_participants table to reference teams
        Schema::table('tournament_participants', function (Blueprint $table) {
            // Drop the team_name and team_members columns
            $table->dropColumn(['team_name', 'team_members']);
            
            // Add team_id column
            $table->foreignId('team_id')->nullable()->after('user_id')->constrained('tournament_teams')->nullOnDelete();
            
            // Add a role column for team members (leader, member)
            $table->enum('role', ['leader', 'member'])->default('member')->after('team_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore the original schema for tournament_participants
        Schema::table('tournament_participants', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->dropColumn(['team_id', 'role']);
            $table->string('team_name')->nullable();
            $table->json('team_members')->nullable();
        });
        
        // Drop the new tables
        Schema::dropIfExists('team_invitations');
        Schema::dropIfExists('tournament_teams');
    }
};