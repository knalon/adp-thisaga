<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Notifications\UserBanned;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'address',
        'profile_picture',
        'is_admin',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::created(function ($user) {
            if (!$user->is_admin) {
                $user->assignRole('user');
            } else {
                $user->assignRole('admin');
            }
        });
    }

    /**
     * Ban this user and send ban notification
     *
     * @param string $reason Reason for the ban
     * @return void
     */
    public function ban(string $reason = ''): void
    {
        $this->update(['is_banned' => true]);

        // Send ban notification
        $this->notify(new UserBanned($reason));

        // Log the ban
        ActivityLog::log(
            'User banned',
            'user_ban',
            $this,
            ['user_id' => $this->id, 'user_email' => $this->email, 'reason' => $reason]
        );
    }

    /**
     * Unban this user
     *
     * @return void
     */
    public function unban(): void
    {
        $this->update(['is_banned' => false]);

        // Log the unban
        ActivityLog::log(
            'User unbanned',
            'user_unban',
            $this,
            ['user_id' => $this->id, 'user_email' => $this->email]
        );
    }

    /**
     * Check if the user can access a Filament panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->hasRole('admin');
        }

        if ($panel->getId() === 'user') {
            return $this->hasRole('user');
        }

        return false;
    }

    /**
     * Get the cars owned by the user.
     */
    public function cars(): HasMany
    {
        return $this->hasMany(Car::class);
    }

    /**
     * Get the bids made by the user.
     */
    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class);
    }

    /**
     * Get the appointments made by the user.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Get the transactions where user is buyer.
     */
    public function purchases(): HasMany
    {
        return $this->hasMany(Transaction::class, 'buyer_id');
    }

    /**
     * Get the transactions where user is seller.
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Transaction::class, 'seller_id');
    }

    /**
     * Get user activity logs.
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }
}
