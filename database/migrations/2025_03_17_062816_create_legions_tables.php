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
        Schema::create('legions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('emblem')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('leader_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('legion_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('legion_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('role', ['leader', 'officer', 'member'])->default('member');
            $table->boolean('is_accepted')->default(false);
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();
            
            // One user can only be in one legion
            $table->unique(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legion_members');
        Schema::dropIfExists('legions');
    }
};