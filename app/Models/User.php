<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Notifications\UserBanned;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'is_active',
        'is_banned',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'profile_photo',
        'is_admin',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_banned' => 'boolean',
        ];
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

    public function cars(): HasMany
    {
        return $this->hasMany(Car::class);
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
}
