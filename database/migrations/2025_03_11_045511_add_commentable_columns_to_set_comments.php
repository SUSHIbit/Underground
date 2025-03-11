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
        Schema::table('set_comments', function (Blueprint $table) {
            // First check if the columns don't already exist
            if (!Schema::hasColumn('set_comments', 'commentable_id')) {
                $table->unsignedBigInteger('commentable_id')->nullable();
            }
            
            if (!Schema::hasColumn('set_comments', 'commentable_type')) {
                $table->string('commentable_type')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('set_comments', function (Blueprint $table) {
            $table->dropColumn(['commentable_id', 'commentable_type']);
        });
    }
};