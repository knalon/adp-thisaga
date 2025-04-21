<?php

namespace App\Enums;

enum VendorStatusEnum: string
{
    case Approved = 'approved';
    case Pending = 'pending';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Approved => __('Approved'),
            self::Pending => __('Pending'),
            self::Rejected => __('Rejected'),
        };
    }


    public function labels(): array
    {
        return [
            self::Approved->value => __('Approved'),
            self::Pending->value => __('Pending'),
            self::Rejected->value => __('Rejected'),
        ];
    }

    public function color(): array
    {
        return [
              'success' => self::Approved->value ,
              'gray' => self::Pending->value ,
              'danger' => self::Rejected->value,            
        ];
    }
}
