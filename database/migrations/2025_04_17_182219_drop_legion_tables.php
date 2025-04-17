<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::dropIfExists('legion_members');
        Schema::dropIfExists('legions');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
