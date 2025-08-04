<?php
// File: database/migrations/xxxx_xx_xx_xxxxxx_create_customers_table.php

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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique(); // Changed from nullable to unique for login
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password'); // Added for authentication
            $table->string('phone', 50);
            $table->text('complete_address');
            $table->rememberToken(); // For "remember me" functionality
            $table->timestamps();

            // Optional: Add index for better performance
            $table->index('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
