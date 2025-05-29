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
        Schema::table('tournament_judge_users', function (Blueprint $table) {
            $table->boolean('grading_completed')->default(false)->after('role');
            $table->timestamp('grading_completed_at')->nullable()->after('grading_completed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tournament_judge_users', function (Blueprint $table) {
            $table->dropColumn(['grading_completed', 'grading_completed_at']);
        });
    }
};