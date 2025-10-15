<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorSubscription extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vendor_id',
        'plan',
        'price_amount',
        'currency',
        'status',
        'started_at',
        'ends_at',
        'payment_reference',
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
        'price_amount' => 'decimal:2',
        'started_at' => 'datetime',
        'ends_at' => 'datetime',
        'paid_at' => 'datetime',
        'approved_at' => 'datetime',
        'payment_expires_at' => 'datetime',
    ];

    /**
     * Get the vendor that owns the subscription.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the admin who approved this subscription.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get payment records for this subscription.
     */
    public function payments()
    {
        return $this->hasMany(SubscriptionPayment::class);
    }

    /**
     * Check if subscription is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && 
               ($this->ends_at === null || $this->ends_at->isFuture());
    }

    /**
     * Check if subscription is expired.
     */
    public function isExpired(): bool
    {
        return $this->ends_at && $this->ends_at->isPast();
    }

    /**
     * Check if payment is completed
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Check if subscription is approved
     */
    public function isApproved(): bool
    {
        return $this->approval_status === 'approved';
    }

    /**
     * Check if subscription is pending approval
     */
    public function isPendingApproval(): bool
    {
        return $this->approval_status === 'pending' && $this->isPaid();
    }

    /**
     * Check if auto-approval should occur
     */
    public function shouldAutoApprove(): bool
    {
        return $this->isPaid() && 
               $this->approval_status === 'pending' &&
               $this->payment_expires_at &&
               now()->isAfter($this->payment_expires_at);
    }

    /**
     * Approve the subscription
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
     * Reject the subscription
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
}

