<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderActivity extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the order that owns the activity
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the user who performed the activity
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Activity type constants
     */
    const TYPE_CREATED = 'created';
    const TYPE_UPDATED = 'updated';
    const TYPE_STATUS_CHANGED = 'status_changed';
    const TYPE_PAYMENT_ADDED = 'payment_added';
    const TYPE_CANCELLED = 'cancelled';
    const TYPE_COMPLETED = 'completed';

    /**
     * Get icon for activity type
     */
    public function getIconAttribute(): string
    {
        return match ($this->activity_type) {
            self::TYPE_CREATED => 'fa-plus-circle',
            self::TYPE_UPDATED => 'fa-edit',
            self::TYPE_STATUS_CHANGED => 'fa-exchange-alt',
            self::TYPE_PAYMENT_ADDED => 'fa-money-bill-wave',
            self::TYPE_CANCELLED => 'fa-times-circle',
            self::TYPE_COMPLETED => 'fa-check-circle',
            default => 'fa-info-circle',
        };
    }

    /**
     * Get color for activity type
     */
    public function getColorAttribute(): string
    {
        return match ($this->activity_type) {
            self::TYPE_CREATED => 'blue',
            self::TYPE_UPDATED => 'yellow',
            self::TYPE_STATUS_CHANGED => 'purple',
            self::TYPE_PAYMENT_ADDED => 'green',
            self::TYPE_CANCELLED => 'red',
            self::TYPE_COMPLETED => 'emerald',
            default => 'gray',
        };
    }
}
