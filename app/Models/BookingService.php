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
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'appointment_date' => 'date',
    ];

    /**
     * Get the booking that owns the service.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the AC types for this booking service.
     */
    public function acTypes()
    {
        return $this->belongsToMany(AcType::class, 'booking_actypes');
    }

    /**
     * Get the booking AC types pivot records.
     */
    public function bookingAcTypes()
    {
        return $this->hasMany(BookingAcType::class);
    }
}
