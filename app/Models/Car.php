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

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('car_images')
            ->useFallbackUrl('/images/default-car.jpg');
    }
}
