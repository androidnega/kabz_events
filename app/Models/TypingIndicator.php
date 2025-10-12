<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypingIndicator extends Model
{
    protected $fillable = [
        'user_id',
        'typing_to_user_id',
        'vendor_id',
        'is_typing',
        'last_typed_at',
    ];

    protected $casts = [
        'is_typing' => 'boolean',
        'last_typed_at' => 'datetime',
    ];

    /**
     * Set user as typing.
     */
    public static function setTyping($userId, $typingToUserId, $vendorId = null)
    {
        return self::updateOrCreate(
            [
                'user_id' => $userId,
                'typing_to_user_id' => $typingToUserId,
            ],
            [
                'vendor_id' => $vendorId,
                'is_typing' => true,
                'last_typed_at' => now(),
            ]
        );
    }

    /**
     * Set user as not typing.
     */
    public static function setNotTyping($userId, $typingToUserId)
    {
        return self::where('user_id', $userId)
            ->where('typing_to_user_id', $typingToUserId)
            ->update([
                'is_typing' => false,
                'last_typed_at' => now(),
            ]);
    }

    /**
     * Check if user is typing.
     */
    public static function isUserTyping($userId, $typingToUserId)
    {
        $indicator = self::where('user_id', $userId)
            ->where('typing_to_user_id', $typingToUserId)
            ->where('is_typing', true)
            ->first();

        if (!$indicator) {
            return false;
        }

        // Consider typing indicator stale after 3 seconds
        if ($indicator->last_typed_at->diffInSeconds(now()) > 3) {
            $indicator->update(['is_typing' => false]);
            return false;
        }

        return true;
    }
}

