<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VipPlan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'price',
        'duration_days',
        'image_limit',
        'free_ads',
        'priority_level',
        'description',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'duration_days' => 'integer',
        'image_limit' => 'integer',
        'free_ads' => 'integer',
        'priority_level' => 'integer',
        'status' => 'boolean',
    ];

    /**
     * Get the subscriptions for this plan.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(VipSubscription::class);
    }

    /**
     * Check if plan is active.
     */
    public function isActive(): bool
    {
        return $this->status === true;
    }

    /**
     * Scope a query to only include active plans.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Get human-readable duration.
     */
    public function getDurationLabel(): string
    {
        if ($this->duration_days === 30) {
            return 'Monthly';
        } elseif ($this->duration_days === 90) {
            return 'Quarterly';
        } elseif ($this->duration_days === 365) {
            return 'Yearly';
        }
        return $this->duration_days . ' days';
    }
}

