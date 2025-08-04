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
