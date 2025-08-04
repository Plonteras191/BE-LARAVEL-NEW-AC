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
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the customer that owns the booking.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the booking status.
     */
    public function status()
    {
        return $this->belongsTo(BookingStatus::class, 'status_id');
    }

    /**
     * Get the booking services for the booking.
     */
    public function bookingServices()
    {
        return $this->hasMany(BookingService::class);
    }

    /**
     * Get the technicians assigned to this booking.
     */
    public function technicians()
    {
        return $this->belongsToMany(Technician::class, 'booking_technicians');
    }

    /**
     * Get the booking technicians pivot records.
     */
    public function bookingTechnicians()
    {
        return $this->hasMany(BookingTechnician::class);
    }

    /**
     * Get the revenue for this booking.
     */
    public function revenue()
    {
        return $this->hasOne(Revenue::class);
    }
}
