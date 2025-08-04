<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type_name',
    ];

    /**
     * Get the booking AC types for the AC type.
     */
    public function bookingAcTypes()
    {
        return $this->hasMany(BookingAcType::class);
    }

    /**
     * Get the booking services that use this AC type.
     */
    public function bookingServices()
    {
        return $this->belongsToMany(BookingService::class, 'booking_actypes', 'ac_type_id', 'booking_service_id')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'customer_id',
        'status_id',
        'cancelled_at',
        'cancellation_reason',
        'cancelled_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Get the customer that owns the booking.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the status of the booking.
     */
    public function status()
    {
        return $this->belongsTo(BookingStatus::class, 'status_id');
    }

    /**
     * Get the customer who cancelled the booking.
     */
    public function cancelledBy()
    {
        return $this->belongsTo(Customer::class, 'cancelled_by');
    }

    /**
     * Get the booking technicians for the booking.
     */
    public function bookingTechnicians()
    {
        return $this->hasMany(BookingTechnician::class);
    }

    /**
     * Get the technicians assigned to this booking.
     */
    public function technicians()
    {
        return $this->belongsToMany(Technician::class, 'booking_technicians');
    }

    /**
     * Get the booking services for the booking.
     */
    public function bookingServices()
    {
        return $this->hasMany(BookingService::class);
    }

    /**
     * Get the revenue for the booking.
     */
    public function revenue()
    {
        return $this->hasOne(Revenue::class);
    }

    /**
     * Scope a query to only include cancelled bookings.
     */
    public function scopeCancelled($query)
    {
        return $query->whereNotNull('cancelled_at');
    }

    /**
     * Scope a query to only include active bookings.
     */
    public function scopeActive($query)
    {
        return $query->whereNull('cancelled_at');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingAcType extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'booking_actypes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'booking_service_id',
        'ac_type_id',
        'quantity',
    ];

    /**
     * Get the booking service that owns the booking AC type.
     */
    public function bookingService()
    {
        return $this->belongsTo(BookingService::class);
    }

    /**
     * Get the AC type that owns the booking AC type.
     */
    public function acType()
    {
        return $this->belongsTo(AcType::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingReschedule extends Model
{
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'booking_service_id',
        'old_appointment_date',
        'old_appointment_time',
        'new_appointment_date',
        'new_appointment_time',
        'reschedule_reason',
        'rescheduled_by',
        'rescheduled_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'old_appointment_date' => 'date',
        'old_appointment_time' => 'datetime:H:i',
        'new_appointment_date' => 'date',
        'new_appointment_time' => 'datetime:H:i',
        'rescheduled_at' => 'datetime',
    ];

    /**
     * Get the booking service that owns the booking reschedule.
     */
    public function bookingService()
    {
        return $this->belongsTo(BookingService::class);
    }

    /**
     * Get the customer who rescheduled the booking.
     */
    public function rescheduledBy()
    {
        return $this->belongsTo(Customer::class, 'rescheduled_by');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingService extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'booking_id',
        'service_type',
        'appointment_date',
        'appointment_time',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'appointment_date' => 'date',
        'appointment_time' => 'datetime:H:i',
    ];

    /**
     * Get the booking that owns the booking service.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the booking AC types for the booking service.
     */
    public function bookingAcTypes()
    {
        return $this->hasMany(BookingAcType::class);
    }

    /**
     * Get the AC types for this booking service.
     */
    public function acTypes()
    {
        return $this->belongsToMany(AcType::class, 'booking_actypes', 'booking_service_id', 'ac_type_id')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

    /**
     * Get the booking reschedules for the booking service.
     */
    public function bookingReschedules()
    {
        return $this->hasMany(BookingReschedule::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingStatus extends Model
{
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'status_name',
    ];

    /**
     * Get the bookings for the status.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'status_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingTechnician extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'booking_id',
        'technician_id',
    ];

    /**
     * Get the booking that owns the booking technician.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the technician that owns the booking technician.
     */
    public function technician()
    {
        return $this->belongsTo(Technician::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'complete_address',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the bookings for the customer.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get the bookings cancelled by this customer.
     */
    public function cancelledBookings()
    {
        return $this->hasMany(Booking::class, 'cancelled_by');
    }

    /**
     * Get the booking reschedules made by this customer.
     */
    public function bookingReschedules()
    {
        return $this->hasMany(BookingReschedule::class, 'rescheduled_by');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Revenue extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'revenue';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'booking_id',
        'revenue_date',
        'total_revenue',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'revenue_date' => 'date',
        'total_revenue' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the booking that owns the revenue.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Technician extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Get the booking technicians for the technician.
     */
    public function bookingTechnicians()
    {
        return $this->hasMany(BookingTechnician::class);
    }

    /**
     * Get the bookings assigned to this technician.
     */
    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_technicians');
    }
}
