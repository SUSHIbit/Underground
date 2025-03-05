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
        Schema::create('challenge_prerequisites', function (Blueprint $table) {
            $table->foreignId('challenge_id')->constrained('challenge_details')->onDelete('cascade');
            $table->foreignId('prerequisite_set_id')->constrained('sets');
            $table->timestamps();
            $table->primary(['challenge_id', 'prerequisite_set_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challenge_prerequisites');
    }
};
