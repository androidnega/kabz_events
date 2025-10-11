<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorResponseTime extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'client_message_id',
        'vendor_reply_id',
        'response_time_minutes',
    ];

    /**
     * Get the vendor.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the client message.
     */
    public function clientMessage(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'client_message_id');
    }

    /**
     * Get the vendor reply.
     */
    public function vendorReply(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'vendor_reply_id');
    }

    /**
     * Calculate average response time for a vendor.
     */
    public static function getAverageResponseTime($vendorId): ?string
    {
        $average = self::where('vendor_id', $vendorId)
            ->avg('response_time_minutes');

        if (!$average) {
            return null;
        }

        $average = round($average);

        if ($average < 60) {
            return "Usually replies in {$average} minute" . ($average > 1 ? 's' : '');
        } elseif ($average < 1440) {
            $hours = round($average / 60);
            return "Usually replies in {$hours} hour" . ($hours > 1 ? 's' : '');
        } else {
            $days = round($average / 1440);
            return "Usually replies in {$days} day" . ($days > 1 ? 's' : '');
        }
    }
}

