<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_subscription_id',
        'paystack_reference',
        'payment_status',
        'amount',
        'currency',
        'payment_channel',
        'customer_email',
        'metadata',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the subscription this payment belongs to
     */
    public function vendorSubscription(): BelongsTo
    {
        return $this->belongsTo(VendorSubscription::class);
    }

    /**
     * Check if payment is successful
     */
    public function isSuccessful(): bool
    {
        return $this->payment_status === 'success';
    }

    /**
     * Mark payment as successful
     */
    public function markAsPaid($channel = null)
    {
        $this->update([
            'payment_status' => 'success',
            'payment_channel' => $channel,
            'paid_at' => now(),
        ]);
    }

    /**
     * Mark payment as failed
     */
    public function markAsFailed()
    {
        $this->update([
            'payment_status' => 'failed',
        ]);
    }
}
