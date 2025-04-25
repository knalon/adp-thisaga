<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bid extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'car_id',
        'appointment_id',
        'amount',
        'status', // pending, accepted, rejected, outbid
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Scope a query to get the highest bid for a car.
     */
    public function scopeHighestBid($query, $carId)
    {
        return $query->where('car_id', $carId)
            ->orderBy('amount', 'desc')
            ->first();
    }

    /**
     * Scope a query to get all active bids for a car.
     */
    public function scopeActiveBids($query, $carId)
    {
        return $query->where('car_id', $carId)
            ->whereIn('status', ['pending', 'accepted'])
            ->orderBy('amount', 'desc');
    }
}
