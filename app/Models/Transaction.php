<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    public const SHIPPING_STATUS = [
        'pending' => 'Pending',
        'in_transit' => 'In Transit',
        'delivered' => 'Delivered',
        'failed' => 'Failed'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'car_id',
        'buyer_id',
        'seller_id',
        'bid_id',
        'amount',
        'payment_method',
        'transaction_reference',
        'status',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Get the car that the transaction is for.
     */
    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    /**
     * Get the buyer for the transaction.
     */
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * Get the seller for the transaction.
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Get the bid associated with the transaction.
     */
    public function bid(): BelongsTo
    {
        return $this->belongsTo(Bid::class);
    }

    /**
     * Scope a query to only include pending transactions.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include completed transactions.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include failed transactions.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope a query to only include refunded transactions.
     */
    public function scopeRefunded($query)
    {
        return $query->where('status', 'refunded');
    }
}
