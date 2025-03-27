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
        // For MySQL
        // This modifies the ENUM field to include 'admin' as a valid value
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('student', 'lecturer', 'accessor', 'admin') NOT NULL DEFAULT 'student'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to the original ENUM without 'admin'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('student', 'lecturer', 'accessor') NOT NULL DEFAULT 'student'");
    }
};