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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_judge')->default(false)->after('role');
        });

        // Migrate existing users with 'judge' role to the new structure
        DB::table('users')
            ->where('role', 'judge')
            ->update([
                'role' => 'student', // Default role, you might want to adjust this
                'is_judge' => true
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Convert users with is_judge back to the old structure
        DB::table('users')
            ->where('is_judge', true)
            ->update(['role' => 'judge']);

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_judge');
        });
    }
};