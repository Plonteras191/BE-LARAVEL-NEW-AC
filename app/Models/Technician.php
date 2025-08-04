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
