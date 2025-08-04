<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookingStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('booking_statuses')->insertOrIgnore([
            ['id' => 1, 'status_name' => 'pending',     'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'status_name' => 'accepted',    'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'status_name' => 'completed',   'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'status_name' => 'rescheduled', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'status_name' => 'cancelled',   'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
