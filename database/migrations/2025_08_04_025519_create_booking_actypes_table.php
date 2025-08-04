<?php
// File: database/migrations/xxxx_xx_xx_xxxxxx_create_booking_actypes_table.php

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
        Schema::create('booking_actypes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_service_id')->constrained()->onDelete('cascade');
            $table->foreignId('ac_type_id')->constrained()->onDelete('restrict');
            $table->integer('quantity')->default(1); // Added to track how many units of each AC type
            $table->timestamps(); // Added for tracking

            $table->unique(['booking_service_id', 'ac_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_actypes');
    }
};
