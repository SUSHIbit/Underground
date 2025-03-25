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
        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->boolean('is_retake')->default(false)->after('completed');
            $table->integer('ue_points_spent')->default(0)->after('is_retake');
            $table->foreignId('original_attempt_id')->nullable()->after('ue_points_spent')
                  ->references('id')->on('quiz_attempts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->dropForeign(['original_attempt_id']);
            $table->dropColumn(['is_retake', 'ue_points_spent', 'original_attempt_id']);
        });
    }
};