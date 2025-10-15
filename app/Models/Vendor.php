<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Vendor extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'business_name',
        'slug',
        'description',
        'sample_work_images',
        'sample_work_title',
        'preview_image',
        'sample_work_video',
        'phone',
        'whatsapp',
        'website',
        'address',
        'latitude',
        'longitude',
        'is_verified',
        'tour_completed',
        'verified_at',
        'verification_doc_path',
        'rating_cached',
        'profile_photo',
        'business_name_changes_count',
        'last_business_name_change_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_verified' => 'boolean',
        'tour_completed' => 'boolean',
        'verified_at' => 'datetime',
        'rating_cached' => 'decimal:2',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'sample_work_images' => 'array',
        'last_business_name_change_at' => 'datetime',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (Vendor $vendor) {
            if (empty($vendor->slug)) {
                $vendor->slug = Str::slug($vendor->business_name);
                
                // Ensure slug is unique
                $originalSlug = $vendor->slug;
                $count = 1;
                while (static::where('slug', $vendor->slug)->exists()) {
                    $vendor->slug = $originalSlug . '-' . $count++;
                }
            }
        });
    }

    /**
     * Get the user that owns the vendor.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the services for the vendor.
     */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    /**
     * Get the reviews for the vendor.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the callback requests for the vendor.
     */
    public function callbackRequests(): HasMany
    {
        return $this->hasMany(CallbackRequest::class);
    }

    /**
     * Get the verification request for the vendor.
     */
    public function verificationRequest(): HasOne
    {
        return $this->hasOne(VerificationRequest::class);
    }

    /**
     * Get the subscriptions for the vendor.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(VendorSubscription::class);
    }

    /**
     * Get the VIP subscriptions for the vendor.
     */
    public function vipSubscriptions(): HasMany
    {
        return $this->hasMany(VipSubscription::class);
    }

    /**
     * Get the featured ads for the vendor.
     */
    public function featuredAds(): HasMany
    {
        return $this->hasMany(FeaturedAd::class);
    }

    /**
     * Get the active subscription for the vendor.
     */
    public function activeSubscription(): ?VendorSubscription
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            })
            ->latest('ends_at')
            ->first();
    }

    /**
     * Get the active VIP subscription for the vendor.
     */
    public function activeVipSubscription(): ?VipSubscription
    {
        return $this->vipSubscriptions()
            ->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->latest('end_date')
            ->first();
    }

    /**
     * Check if vendor has VIP/Premium subscription.
     */
    public function hasVipSubscription(): bool
    {
        // Check new VIP subscriptions first
        $vipSubscription = $this->activeVipSubscription();
        if ($vipSubscription) {
            return true;
        }
        
        // Fallback to old subscription system
        $subscription = $this->activeSubscription();
        return $subscription && in_array(strtolower($subscription->plan), ['vip', 'premium', 'pro']);
    }

    /**
     * Check if vendor has active VIP badge.
     */
    public function hasVipBadge(): bool
    {
        return $this->activeVipSubscription() !== null;
    }

    /**
     * Get VIP tier name (Bronze, Silver, Gold, Platinum).
     */
    public function getVipTier(): ?string
    {
        $subscription = $this->activeVipSubscription();
        return $subscription?->vipPlan?->name;
    }

    /**
     * Get VIP priority level for sorting (higher is better).
     * Platinum = 4, Gold = 3, Silver = 2, Bronze = 1, None = 0
     */
    public function getVipPriority(): int
    {
        $subscription = $this->activeVipSubscription();
        return $subscription?->vipPlan?->priority_level ?? 0;
    }

    /**
     * Check if vendor has priority status (verified OR VIP).
     */
    public function hasPriority(): bool
    {
        return $this->is_verified || $this->hasActiveVip();
    }

    /**
     * Check if vendor has active VIP (alias for hasVipBadge).
     */
    public function hasActiveVip(): bool
    {
        return $this->hasVipBadge();
    }

    /**
     * Get combined ranking score for prioritization.
     * Factors: VIP priority (40%), Verification (30%), Rating (20%), Reviews (10%)
     */
    public function getRankingScore(): float
    {
        $score = 0;
        
        // VIP Priority: 0-40 points (Platinum=40, Gold=30, Silver=20, Bronze=10)
        $vipPriority = $this->getVipPriority();
        $score += ($vipPriority * 10); // 4*10=40, 3*10=30, etc.
        
        // Verification: 30 points
        if ($this->is_verified) {
            $score += 30;
        }
        
        // Rating: 0-20 points (5.0 rating = 20 points)
        $score += ($this->rating_cached ?? 0) * 4; // 5.0 * 4 = 20
        
        // Reviews count: 0-10 points (capped at 10)
        $reviewCount = $this->reviews()->count();
        $score += min($reviewCount, 10);
        
        return $score;
    }

    /**
     * Scope to apply VIP-based ranking to query.
     * Usage: Vendor::ranked()->get()
     */
    public function scopeRanked(Builder $query): Builder
    {
        return \App\Services\VendorRankingService::applyRanking($query);
    }

    /**
     * Scope to apply ranking with default sorting.
     * Usage: Vendor::rankedWithSort()->get()
     */
    public function scopeRankedWithSort(Builder $query): Builder
    {
        return \App\Services\VendorRankingService::applyRankingWithSort($query);
    }

    /**
     * Get vendor's priority level for search ranking.
     */
    public function getPriorityLevel(): int
    {
        $vipSub = $this->activeVipSubscription();
        if ($vipSub && $vipSub->vipPlan) {
            return $vipSub->vipPlan->priority_level;
        }
        return 1; // Normal priority
    }

    /**
     * Get maximum allowed sample work images based on subscription.
     */
    public function getMaxSampleImages(): int
    {
        // Check VIP subscription limit first
        $vipSub = $this->activeVipSubscription();
        if ($vipSub && $vipSub->vipPlan) {
            return $vipSub->vipPlan->image_limit;
        }
        
        // VIP/Verified vendors can upload more, free users limited to 5
        if ($this->hasVipSubscription() || $this->is_verified) {
            return 20; // VIP/Verified get 20 images
        }
        return 5; // Free users get 5 images
    }

    /**
     * Check if vendor can upload videos.
     */
    public function canUploadVideo(): bool
    {
        return $this->hasVipSubscription();
    }

    /**
     * Get image URL (handles both Cloudinary and local storage).
     */
    public function getImageUrl($imageData): ?string
    {
        return get_image_url($imageData);
    }

    /**
     * Get preview image URL.
     */
    public function getPreviewImageUrl(): ?string
    {
        return get_image_url($this->preview_image);
    }

    /**
     * Get video URL.
     */
    public function getVideoUrl(): ?string
    {
        return get_image_url($this->sample_work_video);
    }

    /**
     * Get the region for the vendor.
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * Get the district for the vendor.
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    /**
     * Get the town for the vendor.
     */
    public function town(): BelongsTo
    {
        return $this->belongsTo(Town::class);
    }

    /**
     * Check if vendor can change business name
     */
    public function canChangeBusinessName(): bool
    {
        // Maximum 3 name changes per year
        if ($this->business_name_changes_count >= 3) {
            if (!$this->last_business_name_change_at || $this->last_business_name_change_at->addYear()->isFuture()) {
                return false;
            }
            // Reset counter if a year has passed
            $this->business_name_changes_count = 0;
            $this->save();
        }
        return true;
    }

    /**
     * Get remaining business name changes for the current period
     */
    public function remainingBusinessNameChanges(): int
    {
        if (!$this->last_business_name_change_at || $this->last_business_name_change_at->addYear()->isPast()) {
            return 3;
        }
        return max(0, 3 - $this->business_name_changes_count);
    }

    /**
     * Check if vendor can show WhatsApp contact
     */
    public function canShowWhatsApp(): bool
    {
        // Must be verified OR have an active subscription
        if ($this->is_verified) {
            return true;
        }

        $activeSubscription = $this->activeSubscription();
        return $activeSubscription && $activeSubscription->plan !== 'Free';
    }
}
