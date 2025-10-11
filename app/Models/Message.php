<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'sender_type',
        'receiver_id',
        'receiver_type',
        'vendor_id',
        'message',
        'media_type',
        'media_url',
        'is_read',
        'read_at',
        'deleted_by_sender',
        'deleted_by_receiver',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'deleted_by_sender' => 'boolean',
        'deleted_by_receiver' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the sender user.
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the receiver user.
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Get the vendor.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Scope to get messages for a specific conversation.
     */
    public function scopeConversation($query, $userId1, $userId2, $vendorId)
    {
        return $query->where('vendor_id', $vendorId)
            ->where(function ($q) use ($userId1, $userId2) {
                $q->where(function ($query) use ($userId1, $userId2) {
                    $query->where('sender_id', $userId1)
                          ->where('receiver_id', $userId2);
                })->orWhere(function ($query) use ($userId1, $userId2) {
                    $query->where('sender_id', $userId2)
                          ->where('receiver_id', $userId1);
                });
            });
    }

    /**
     * Scope to get unread messages.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope to exclude deleted messages for a specific user.
     */
    public function scopeVisibleTo($query, $userId, $userType)
    {
        return $query->where(function ($q) use ($userId, $userType) {
            if ($userType === 'sender') {
                $q->where('sender_id', $userId)->where('deleted_by_sender', false);
            } else {
                $q->where('receiver_id', $userId)->where('deleted_by_receiver', false);
            }
        });
    }

    /**
     * Mark message as read.
     */
    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    /**
     * Get human-readable time ago.
     */
    public function timeAgo(): string
    {
        $diff = now()->diffInMinutes($this->created_at);
        
        if ($diff < 1) {
            return 'Just now';
        } elseif ($diff < 60) {
            return $diff . ' min' . ($diff > 1 ? 's' : '') . ' ago';
        } elseif ($diff < 1440) {
            $hours = floor($diff / 60);
            return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
        } elseif ($diff < 10080) {
            $days = floor($diff / 1440);
            return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
        } else {
            return $this->created_at->format('M d, Y');
        }
    }
}
