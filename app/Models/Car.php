<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Support\Str;

class Car extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'make',
        'model',
        'registration_year',
        'price',
        'description',
        'color',
        'mileage',
        'transmission',
        'fuel_type',
        'is_active',
        'is_approved',
        'is_sold',
        'sold_at',
        'slug',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_approved' => 'boolean',
        'is_sold' => 'boolean',
        'sold_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($car) {
            $car->slug = Str::slug($car->make . ' ' . $car->model . ' ' . $car->registration_year . ' ' . Str::random(6));
        });
    }

    /**
     * Activate this car listing
     *
     * @return void
     */
    public function activate(): void
    {
        $this->update(['is_active' => true]);

        // Log the activation
        ActivityLog::log(
            'Car listing activated',
            'car_activate',
            $this,
            ['car_id' => $this->id, 'car_make' => $this->make, 'car_model' => $this->model]
        );
    }

    /**
     * Deactivate this car listing
     *
     * @return void
     */
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);

        // Log the deactivation
        ActivityLog::log(
            'Car listing deactivated',
            'car_deactivate',
            $this,
            ['car_id' => $this->id, 'car_make' => $this->make, 'car_model' => $this->model]
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class);
    }

    /**
     * Get the current highest bid for this car
     *
     * @return \App\Models\Bid|null
     */
    public function getHighestBid()
    {
        return $this->bids()
            ->whereIn('status', ['pending', 'accepted'])
            ->orderBy('amount', 'desc')
            ->first();
    }

    /**
     * Get all active bids for this car
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveBids()
    {
        return $this->bids()
            ->whereIn('status', ['pending', 'accepted'])
            ->orderBy('amount', 'desc')
            ->get();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('car_images')
            ->useFallbackUrl('/images/default-car.jpg');
    }
}
