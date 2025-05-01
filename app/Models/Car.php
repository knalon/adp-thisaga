<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Support\Str;
use App\Enums\CarStatus;
use App\Enums\FuelType;
use App\Enums\TransmissionType;

class Car extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'make',
        'model',
        'year',
        'color',
        'transmission',
        'price',
        'description',
        'mileage',
        'fuel_type',
        'condition',
        'is_approved',
        'is_active',
        'is_sold',
        'is_pending_sale',
        'sold_at',
        'slug',
        'approval_status',
        'rejection_reason',
        'status',
        'name',
        'images'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'year' => 'integer',
        'price' => 'decimal:2',
        'mileage' => 'integer',
        'is_approved' => 'boolean',
        'is_active' => 'boolean',
        'is_sold' => 'boolean',
        'is_pending_sale' => 'boolean',
        'sold_at' => 'datetime',
        'images' => 'array',
        'transmission' => TransmissionType::class,
        'fuel_type' => FuelType::class,
        'status' => CarStatus::class,
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($car) {
            $car->slug = Str::slug($car->make . ' ' . $car->model . ' ' . $car->year . ' ' . Str::random(6));
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

    /**
     * Get the user that owns the car.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the images for the car.
     */
    public function images(): HasMany
    {
        return $this->hasMany(CarImage::class);
    }

    /**
     * Get the primary image for the car.
     */
    public function getPrimaryImage()
    {
        return $this->images()->where('is_primary', true)->first() ?:
               $this->images()->first();
    }

    /**
     * Get the bids for the car.
     */
    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class);
    }

    /**
     * Get the appointments for the car.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Get the transaction for the car.
     */
    public function transaction(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Scope a query to only include active cars.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include approved cars.
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope a query to only include not sold cars.
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_sold', false);
    }

    /**
     * Scope a query to filter by make.
     */
    public function scopeByMake($query, $make)
    {
        return $query->where('make', $make);
    }

    /**
     * Scope a query to filter by model.
     */
    public function scopeByModel($query, $model)
    {
        return $query->where('model', $model);
    }

    /**
     * Scope a query to filter by year.
     */
    public function scopeByYear($query, $year)
    {
        return $query->where('year', $year);
    }

    /**
     * Scope a query to filter by price range.
     */
    public function scopeByPriceRange($query, $min, $max)
    {
        if ($min) {
            $query->where('price', '>=', $min);
        }

        if ($max) {
            $query->where('price', '<=', $max);
        }

        return $query;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('car_images')
            ->useFallbackUrl('/images/default-car.jpg');
    }
}
