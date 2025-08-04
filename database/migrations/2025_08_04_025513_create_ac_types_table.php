<?php
// File: database/migrations/xxxx_xx_xx_xxxxxx_create_ac_types_table.php

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
        Schema::create('ac_types', function (Blueprint $table) {
            $table->id();
            $table->string('type_name', 50)->unique();
            $table->timestamps(); // Added for consistency
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ac_types');
    }
};
