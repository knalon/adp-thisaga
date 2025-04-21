<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\VendorStatusEnum;

class Vendor extends Model
{
    protected $primaryKey = 'user_id';

    public function scopeEligibleForPayout(Builder $query): Builder
    {
        return $query->where('status', VendorStatusEnum::Approved);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
