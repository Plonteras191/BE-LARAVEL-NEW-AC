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
