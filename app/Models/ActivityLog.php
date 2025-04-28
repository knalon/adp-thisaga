<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'action',
        'entity_type',
        'entity_id',
        'details',
        'ip_address',
        'user_agent',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'details' => 'array',
    ];

    /**
     * Get the user that performed the action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the related entity based on entity_type.
     */
    public function entity()
    {
        if (!$this->entity_type || !$this->entity_id) {
            return null;
        }

        $modelClass = "App\\Models\\{$this->entity_type}";
        
        if (!class_exists($modelClass)) {
            return null;
        }

        return $modelClass::find($this->entity_id);
    }

    /**
     * Scope a query to only include logs for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include logs for a specific action.
     */
    public function scopeWithAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope a query to only include logs for a specific entity type.
     */
    public function scopeForEntityType($query, $entityType)
    {
        return $query->where('entity_type', $entityType);
    }

    /**
     * Scope a query to only include logs for a specific entity.
     */
    public function scopeForEntity($query, $entityType, $entityId)
    {
        return $query->where('entity_type', $entityType)
                     ->where('entity_id', $entityId);
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
