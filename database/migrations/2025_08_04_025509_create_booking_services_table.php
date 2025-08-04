<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->string('service_type', 50);
            $table->date('appointment_date');
            $table->timestamps();  // to audit reschedules
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_services');
    }
};
