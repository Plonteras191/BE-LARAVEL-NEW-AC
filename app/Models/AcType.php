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
