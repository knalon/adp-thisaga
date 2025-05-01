<?php

namespace App\Enums;

enum CarStatus: string
{
    case AVAILABLE = 'available';
    case SOLD = 'sold';
    case PENDING = 'pending';
    case RESERVED = 'reserved';

    public function label(): string
    {
        return match($this) {
            self::AVAILABLE => 'Available',
            self::SOLD => 'Sold',
            self::PENDING => 'Pending',
            self::RESERVED => 'Reserved',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::AVAILABLE => 'success',
            self::SOLD => 'danger',
            self::PENDING => 'warning',
            self::RESERVED => 'info',
        };
    }
} 