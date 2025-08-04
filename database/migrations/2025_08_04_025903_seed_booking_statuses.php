<?php
// File: database/migrations/xxxx_xx_xx_xxxxxx_seed_booking_statuses.php
// Create this with: php artisan make:migration seed_booking_statuses

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
        // Seed the booking statuses
        DB::table('booking_statuses')->insert([
            ['id' => 1, 'status_name' => 'PENDING'],
            ['id' => 2, 'status_name' => 'CONFIRMED'],
            ['id' => 3, 'status_name' => 'COMPLETED'],
            ['id' => 4, 'status_name' => 'CANCELLED'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('booking_statuses')->whereIn('status_name', [
            'PENDING', 'CONFIRMED', 'COMPLETED', 'CANCELLED'
        ])->delete();
    }
};
