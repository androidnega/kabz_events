<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CallbackRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'client_name',
        'client_phone',
        'status',
        'notes',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    /**
     * Get the vendor for this callback request.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Scope for pending requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for completed requests.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Mark the callback request as completed.
     */
    public function markAsCompleted(?string $notes = null): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'notes' => $notes,
        ]);
    }

    /**
     * Mark the callback request as cancelled.
     */
    public function markAsCancelled(?string $notes = null): void
    {
        $this->update([
            'status' => 'cancelled',
            'notes' => $notes,
        ]);
    }
}
