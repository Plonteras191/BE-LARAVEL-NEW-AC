<?php
// File: database/migrations/xxxx_xx_xx_xxxxxx_create_bookings_table.php

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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('status_id')->default(1);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Added for tracking changes
            $table->timestamp('cancelled_at')->nullable(); // Track when booking was cancelled
            $table->text('cancellation_reason')->nullable(); // Track why booking was cancelled
            $table->foreignId('cancelled_by')->nullable()->constrained('customers'); // Who cancelled it

            $table->foreign('status_id')->references('id')->on('booking_statuses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
