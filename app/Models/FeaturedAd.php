<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeaturedAd extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vendor_id',
        'service_id',
        'placement',
        'headline',
        'description',
        'image_path',
        'start_date',
        'end_date',
        'status',
        'price',
        'payment_ref',
        'views',
        'clicks',
        'admin_notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'price' => 'decimal:2',
        'views' => 'integer',
        'clicks' => 'integer',
    ];

    /**
     * Get the vendor that owns the featured ad.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the service being featured.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Check if the ad is currently active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active' 
            && $this->start_date->isPast() 
            && $this->end_date->isFuture();
    }

    /**
     * Check if the ad has expired.
     */
    public function isExpired(): bool
    {
        return $this->end_date->isPast();
    }

    /**
     * Get the image URL.
     */
    public function getImageUrl(): ?string
    {
        return get_image_url($this->image_path);
    }

    /**
     * Increment view count.
     */
    public function incrementViews(): void
    {
        $this->increment('views');
    }

    /**
     * Increment click count.
     */
    public function incrementClicks(): void
    {
        $this->increment('clicks');
    }

    /**
     * Get click-through rate (CTR).
     */
    public function getCTR(): float
    {
        if ($this->views === 0) {
            return 0.0;
        }
        return round(($this->clicks / $this->views) * 100, 2);
    }

    /**
     * Scope a query to only include active ads.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    /**
     * Scope a query to only include ads by placement.
     */
    public function scopeByPlacement($query, string $placement)
    {
        return $query->where('placement', $placement);
    }
}

