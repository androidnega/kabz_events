<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Town extends Model
{
    use HasFactory;

    protected $fillable = ['district_id', 'name', 'slug'];

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }
}
