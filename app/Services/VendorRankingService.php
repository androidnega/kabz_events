<?php

namespace App\Services;

use App\Models\Vendor;
use Illuminate\Database\Eloquent\Builder;

/**
 * Centralized Vendor Ranking Service
 * Provides dynamic, scalable vendor prioritization logic
 */
class VendorRankingService
{
    /**
     * Priority score weights (configurable)
     * Higher numbers = higher priority
     */
    private const VIP_BASE_SCORE = 10;
    private const SUBSCRIPTION_SCORE = 3;
    private const VERIFIED_SCORE = 2;
    private const FREE_SCORE = 1;

    /**
     * Apply VIP-based ranking to a vendor query.
     * This is the SINGLE source of truth for vendor prioritization.
     * 
     * Usage: VendorRankingService::applyRanking($query)
     * 
     * @param Builder $query
     * @return Builder
     */
    public static function applyRanking(Builder $query): Builder
    {
        return $query
            // Join active vendor subscriptions (legacy)
            ->leftJoin('vendor_subscriptions', function ($join) {
                $join->on('vendors.id', '=', 'vendor_subscriptions.vendor_id')
                     ->where('vendor_subscriptions.status', '=', 'active')
                     ->where(function ($q) {
                         $q->whereNull('vendor_subscriptions.ends_at')
                           ->orWhere('vendor_subscriptions.ends_at', '>=', now());
                     });
            })
            // Join active VIP subscriptions (new system)
            ->leftJoin('vip_subscriptions', function ($join) {
                $join->on('vendors.id', '=', 'vip_subscriptions.vendor_id')
                     ->where('vip_subscriptions.status', '=', 'active')
                     ->where('vip_subscriptions.start_date', '<=', now())
                     ->where('vip_subscriptions.end_date', '>=', now());
            })
            // Join VIP plans to get priority level
            ->leftJoin('vip_plans', 'vip_subscriptions.vip_plan_id', '=', 'vip_plans.id')
            // Calculate dynamic priority score
            ->selectRaw('vendors.*, 
                CASE 
                    WHEN vip_plans.priority_level IS NOT NULL THEN (' . self::VIP_BASE_SCORE . ' + vip_plans.priority_level)
                    WHEN vendor_subscriptions.id IS NOT NULL THEN ' . self::SUBSCRIPTION_SCORE . '
                    WHEN vendors.is_verified = 1 THEN ' . self::VERIFIED_SCORE . '
                    ELSE ' . self::FREE_SCORE . '
                END as priority_score');
    }

    /**
     * Apply ranking and default sorting (priority DESC, rating DESC).
     * 
     * @param Builder $query
     * @return Builder
     */
    public static function applyRankingWithSort(Builder $query): Builder
    {
        return self::applyRanking($query)
            ->orderByDesc('priority_score')
            ->orderByDesc('rating_cached');
    }

    /**
     * Get priority score for a specific vendor (for testing/debugging).
     * 
     * @param Vendor $vendor
     * @return int
     */
    public static function getVendorScore(Vendor $vendor): int
    {
        // Check VIP subscription (active and not expired)
        $vipSub = $vendor->vipSubscriptions()
            ->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->with('vipPlan')
            ->first();

        if ($vipSub && $vipSub->vipPlan) {
            return self::VIP_BASE_SCORE + $vipSub->vipPlan->priority_level;
        }

        // Check legacy subscription (active and not expired)
        $subscription = $vendor->subscriptions()
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('ends_at')
                  ->orWhere('ends_at', '>=', now());
            })
            ->first();

        if ($subscription) {
            return self::SUBSCRIPTION_SCORE;
        }

        // Check verified status
        if ($vendor->is_verified) {
            return self::VERIFIED_SCORE;
        }

        // Default: free vendor
        return self::FREE_SCORE;
    }

    /**
     * Check if subscription/verification has expired and update priority dynamically.
     * 
     * @param Vendor $vendor
     * @return bool Whether vendor lost priority
     */
    public static function checkAndUpdateExpiredStatus(Vendor $vendor): bool
    {
        $hadPriority = $vendor->hasPriority();
        
        // Refresh relationships to get current status
        $vendor->load(['vipSubscriptions.vipPlan', 'subscriptions']);
        
        $hasPriorityNow = $vendor->hasPriority();
        
        // Return true if vendor lost priority (expired)
        return $hadPriority && !$hasPriorityNow;
    }

    /**
     * Get ranking configuration (for admin UI to customize weights).
     * 
     * @return array
     */
    public static function getRankingConfig(): array
    {
        return [
            'vip_base_score' => self::VIP_BASE_SCORE,
            'subscription_score' => self::SUBSCRIPTION_SCORE,
            'verified_score' => self::VERIFIED_SCORE,
            'free_score' => self::FREE_SCORE,
            'explanation' => 'VIP vendors get base score + tier level (1-4). Others get fixed scores.',
        ];
    }
}

