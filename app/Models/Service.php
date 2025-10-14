<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vendor_id',
        'category_id',
        'title',
        'description',
        'price_min',
        'price_max',
        'pricing_type',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price_min' => 'decimal:2',
        'price_max' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the vendor that owns the service.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the category that owns the service.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the featured ads for this service.
     */
    public function featuredAds(): HasMany
    {
        return $this->hasMany(FeaturedAd::class);
    }

    /**
     * Check if service has active featured ad.
     */
    public function hasFeaturedAd(): bool
    {
        return $this->featuredAds()
            ->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->exists();
    }
}
