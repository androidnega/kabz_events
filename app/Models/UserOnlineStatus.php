<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserOnlineStatus extends Model
{
    use HasFactory;

    protected $table = 'user_online_status';

    protected $fillable = [
        'user_id',
        'is_online',
        'last_seen_at',
        'user_type',
    ];

    protected $casts = [
        'is_online' => 'boolean',
        'last_seen_at' => 'datetime',
    ];

    /**
     * Get the user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get human-readable last seen time.
     */
    public function lastSeenText(): string
    {
        if ($this->is_online) {
            return 'Online';
        }

        if (!$this->last_seen_at) {
            return 'Offline';
        }

        $diff = now()->diffInMinutes($this->last_seen_at);
        
        if ($diff < 1) {
            return 'Active now';
        } elseif ($diff < 60) {
            return $diff . ' min' . ($diff > 1 ? 's' : '') . ' ago';
        } elseif ($diff < 1440) {
            $hours = floor($diff / 60);
            return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
        } elseif ($diff < 10080) {
            $days = floor($diff / 1440);
            return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
        } else {
            return 'Last seen ' . $this->last_seen_at->format('M d');
        }
    }

    /**
     * Update user's online status.
     */
    public static function updateStatus($userId, $userType, $isOnline = true): void
    {
        self::updateOrCreate(
            ['user_id' => $userId],
            [
                'user_type' => $userType,
                'is_online' => $isOnline,
                'last_seen_at' => now(),
            ]
        );
    }
}

