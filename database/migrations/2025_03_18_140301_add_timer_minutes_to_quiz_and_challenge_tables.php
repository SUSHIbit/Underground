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
        Schema::table('quiz_details', function (Blueprint $table) {
            $table->integer('timer_minutes')->nullable()->after('topic_id');
        });

        Schema::table('challenge_details', function (Blueprint $table) {
            $table->integer('timer_minutes')->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quiz_details', function (Blueprint $table) {
            $table->dropColumn('timer_minutes');
        });

        Schema::table('challenge_details', function (Blueprint $table) {
            $table->dropColumn('timer_minutes');
        });
    }
};