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
        // Only add the score column if it doesn't exist
        if (!Schema::hasColumn('tournament_participants', 'score')) {
            Schema::table('tournament_participants', function (Blueprint $table) {
                $table->integer('score')->nullable()->after('submission_url');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only drop the score column if it exists
        if (Schema::hasColumn('tournament_participants', 'score')) {
            Schema::table('tournament_participants', function (Blueprint $table) {
                $table->dropColumn('score');
            });
        }
    }
};