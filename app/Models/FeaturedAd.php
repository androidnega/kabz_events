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
        'payment_status',
        'payment_method',
        'paystack_reference',
        'paid_at',
        'approval_status',
        'approved_by',
        'approved_at',
        'approval_note',
        'payment_expires_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'price' => 'decimal:2',
        'views' => 'integer',
        'clicks' => 'integer',
        'paid_at' => 'datetime',
        'approved_at' => 'datetime',
        'payment_expires_at' => 'datetime',
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
     * Get the admin who approved this ad.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Check if payment is completed
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Check if ad is approved
     */
    public function isApproved(): bool
    {
        return $this->approval_status === 'approved';
    }

    /**
     * Check if ad is pending approval
     */
    public function isPendingApproval(): bool
    {
        return $this->approval_status === 'pending' && $this->isPaid();
    }

    /**
     * Approve the ad
     */
    public function approve($adminId = null, $note = null)
    {
        $this->update([
            'approval_status' => 'approved',
            'approved_by' => $adminId,
            'approved_at' => now(),
            'approval_note' => $note,
            'status' => 'active',
        ]);
    }

    /**
     * Reject the ad
     */
    public function reject($adminId = null, $note = null)
    {
        $this->update([
            'approval_status' => 'rejected',
            'approved_by' => $adminId,
            'approved_at' => now(),
            'approval_note' => $note,
            'status' => 'inactive',
        ]);
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

