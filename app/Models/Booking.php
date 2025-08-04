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
