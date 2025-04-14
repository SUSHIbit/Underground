<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tournaments', function (Blueprint $table) {
            $table->timestamp('published_at')->nullable()->after('reviewed_at');
        });
        
        // Modify the enum to include the new status
        DB::statement("ALTER TABLE tournaments MODIFY COLUMN status ENUM('draft', 'pending_approval', 'approved_unpublished', 'approved', 'rejected') NOT NULL DEFAULT 'draft'");
        
        // Update existing approved tournaments to have a published_at date
        DB::table('tournaments')
            ->where('status', 'approved')
            ->update(['published_at' => DB::raw('reviewed_at')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First update all approved_unpublished to approved
        DB::table('tournaments')
            ->where('status', 'approved_unpublished')
            ->update(['status' => 'approved']);
        
        // Then modify the enum back to original
        DB::statement("ALTER TABLE tournaments MODIFY COLUMN status ENUM('draft', 'pending_approval', 'approved', 'rejected') NOT NULL DEFAULT 'draft'");
        
        Schema::table('tournaments', function (Blueprint $table) {
            $table->dropColumn('published_at');
        });
    }
};