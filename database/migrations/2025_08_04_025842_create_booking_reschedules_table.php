<?php
// File: database/migrations/xxxx_xx_xx_xxxxxx_create_booking_reschedules_table.php
// Create this with: php artisan make:migration create_booking_reschedules_table

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
        Schema::create('booking_reschedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_service_id')->constrained()->onDelete('cascade');
            $table->date('old_appointment_date');
            $table->time('old_appointment_time')->nullable();
            $table->date('new_appointment_date');
            $table->time('new_appointment_time')->nullable();
            $table->text('reschedule_reason')->nullable();
            $table->foreignId('rescheduled_by')->constrained('customers');
            $table->timestamp('rescheduled_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_reschedules');
    }
};
