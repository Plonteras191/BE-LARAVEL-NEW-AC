php artisan make:migration create_customers_table
php artisan make:migration create_technicians_table
php artisan make:migration create_booking_statuses_table
php artisan make:migration create_bookings_table
php artisan make:migration create_booking_technicians_table
php artisan make:migration create_booking_services_table
php artisan make:migration create_ac_types_table
php artisan make:migration create_booking_actypes_table
php artisan make:migration create_revenue_table


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

<?php
// File: database/migrations/xxxx_xx_xx_xxxxxx_create_technicians_table.php

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
        Schema::create('technicians', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps(); // Added for consistency
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('technicians');
    }
};

<?php
// File: database/migrations/xxxx_xx_xx_xxxxxx_create_booking_statuses_table.php

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
        Schema::create('booking_statuses', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('status_name', 50)->unique();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_statuses');
    }
};

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

<?php
// File: database/migrations/xxxx_xx_xx_xxxxxx_create_booking_technicians_table.php

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
        Schema::create('booking_technicians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('technician_id')->constrained()->onDelete('cascade');
            $table->timestamps(); // Added for tracking when technician was assigned

            $table->unique(['booking_id', 'technician_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_technicians');
    }
};

<?php
// File: database/migrations/xxxx_xx_xx_xxxxxx_create_booking_services_table.php

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
        Schema::create('booking_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->string('service_type', 50);
            $table->date('appointment_date');
            $table->time('appointment_time')->nullable(); // Added time for better scheduling
            $table->timestamps(); // Added for tracking changes
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_services');
    }
};

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

<?php
// File: database/migrations/xxxx_xx_xx_xxxxxx_create_revenue_table.php

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
        Schema::create('revenue', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->unique()->constrained()->onDelete('cascade');
            $table->date('revenue_date');
            $table->decimal('total_revenue', 10, 2);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Added for consistency
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('revenue');
    }
};

// ================ NEW MIGRATION FILES NEEDED ================

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
