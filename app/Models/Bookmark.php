<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bookmarks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'vendor_id',
    ];

    /**
     * Get the user that owns the bookmark.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the vendor that is bookmarked.
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}

