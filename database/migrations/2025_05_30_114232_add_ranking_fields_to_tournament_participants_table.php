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
        Schema::table('tournament_participants', function (Blueprint $table) {
            $table->integer('tournament_rank')->nullable()->after('score');
            $table->integer('ue_points_awarded')->nullable()->after('points_awarded');
            $table->boolean('ranking_calculated')->default(false)->after('ue_points_awarded');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tournament_participants', function (Blueprint $table) {
            $table->dropColumn(['tournament_rank', 'ue_points_awarded', 'ranking_calculated']);
        });
    }
};