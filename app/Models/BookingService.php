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
