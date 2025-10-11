<?php

namespace App\Models;

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
        'phone',
        'whatsapp',
        'website',
        'address',
        'latitude',
        'longitude',
        'is_verified',
        'verified_at',
        'verification_doc_path',
        'rating_cached',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'rating_cached' => 'decimal:2',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'sample_work_images' => 'array',
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
}
