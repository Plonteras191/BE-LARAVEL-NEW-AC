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
    ];

    /**
     * Get the booking service that owns this record.
     */
    public function bookingService()
    {
        return $this->belongsTo(BookingService::class);
    }

    /**
     * Get the AC type that owns this record.
     */
    public function acType()
    {
        return $this->belongsTo(AcType::class);
    }
}
