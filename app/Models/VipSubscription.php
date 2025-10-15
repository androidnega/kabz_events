<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VipSubscription extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vendor_id',
        'vip_plan_id',
        'start_date',
        'end_date',
        'status',
        'payment_ref',
        'ads_used',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'ads_used' => 'integer',
    ];

    /**
     * Get the vendor that owns the subscription.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the VIP plan for the subscription.
     */
    public function vipPlan(): BelongsTo
    {
        return $this->belongsTo(VipPlan::class);
    }

    /**
     * Check if subscription is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active' 
            && $this->start_date->isPast() 
            && $this->end_date->isFuture();
    }

    /**
     * Check if subscription is expired.
     */
    public function isExpired(): bool
    {
        return $this->end_date->isPast();
    }

    /**
     * Get remaining free ads.
     */
    public function getRemainingFreeAds(): int
    {
        $plan = $this->vipPlan;
        if (!$plan) {
            return 0;
        }
        return max(0, $plan->free_ads - $this->ads_used);
    }

    /**
     * Check if has free ads available.
     */
    public function hasFreeAds(): bool
    {
        return $this->getRemainingFreeAds() > 0;
    }

    /**
     * Use a free ad slot.
     */
    public function useFreeAd(): bool
    {
        if (!$this->hasFreeAds()) {
            return false;
        }
        $this->increment('ads_used');
        return true;
    }

    /**
     * Scope a query to only include active subscriptions.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }
}

