<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'action_type',
        'subject_type',
        'subject_id',
        'properties',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    /**
     * Get the user that performed the action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subject of the activity.
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Log an activity.
     *
     * @param string $action
     * @param string $actionType
     * @param Model|null $subject
     * @param array $properties
     * @return static
     */
    public static function log(string $action, string $actionType, ?Model $subject = null, array $properties = []): self
    {
        $log = new static;
        $log->action = $action;
        $log->action_type = $actionType;

        if ($subject) {
            $log->subject()->associate($subject);
        }

        $log->properties = $properties;

        if (Auth::check()) {
            $log->user_id = Auth::id();
        }

        if (request()) {
            $log->ip_address = request()->ip();
            $log->user_agent = request()->userAgent();
        }

        $log->save();

        return $log;
    }
}
