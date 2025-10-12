<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'display_id',
        'latitude',
        'longitude',
        'location_name',
        'location_updated_at',
        'preferred_categories',
        'preferred_region',
        'search_radius_km',
        'total_searches',
        'total_vendor_views',
        'profile_photo',
        'name_changes_count',
        'last_name_change_at',
    ];

    /**
     * Boot the model and generate display ID.
     */
    protected static function booted()
    {
        static::created(function ($user) {
            if (!$user->display_id) {
                $user->generateDisplayId();
            }
        });
    }

    /**
     * Generate a display ID for the user based on their role.
     */
    public function generateDisplayId()
    {
        $role = $this->roles->first()?->name ?? 'user';
        
        $prefix = match($role) {
            'super_admin' => 'SA',
            'admin' => 'ADM',
            'vendor' => 'VND',
            'client' => 'CLT',
            default => 'USR',
        };
        
        $displayId = $prefix . '-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
        
        $this->update(['display_id' => $displayId]);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'location_updated_at' => 'datetime',
        'preferred_categories' => 'array',
        'search_radius_km' => 'decimal:2',
        'total_searches' => 'integer',
        'total_vendor_views' => 'integer',
        'last_name_change_at' => 'datetime',
    ];

    /**
     * Get the vendor profile associated with the user.
     */
    public function vendor(): HasOne
    {
        return $this->hasOne(Vendor::class);
    }

    /**
     * Get all notifications for the user.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'notifiable_id')
                    ->where('notifiable_type', 'App\Models\User')
                    ->orderBy('created_at', 'desc');
    }

    /**
     * Get unread notifications for the user.
     */
    public function unreadNotifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'notifiable_id')
                    ->where('notifiable_type', 'App\Models\User')
                    ->whereNull('read_at')
                    ->orderBy('created_at', 'desc');
    }

    /**
     * Check if user can change their name
     */
    public function canChangeName(): bool
    {
        // Maximum 3 name changes per year
        if ($this->name_changes_count >= 3) {
            if (!$this->last_name_change_at || $this->last_name_change_at->addYear()->isFuture()) {
                return false;
            }
            // Reset counter if a year has passed
            $this->name_changes_count = 0;
            $this->save();
        }
        return true;
    }

    /**
     * Get remaining name changes for the current period
     */
    public function remainingNameChanges(): int
    {
        if (!$this->last_name_change_at || $this->last_name_change_at->addYear()->isPast()) {
            return 3;
        }
        return max(0, 3 - $this->name_changes_count);
    }

    /**
     * Get next available name change date
     */
    public function nextNameChangeAvailableAt(): ?\Carbon\Carbon
    {
        if ($this->name_changes_count >= 3 && $this->last_name_change_at) {
            return $this->last_name_change_at->addYear();
        }
        return null;
    }

    /**
     * Get the user's preferences.
     */
    public function preferences(): HasOne
    {
        return $this->hasOne(UserPreference::class);
    }

    /**
     * Get the user's interactions.
     */
    public function interactions(): HasMany
    {
        return $this->hasMany(UserInteraction::class);
    }

    /**
     * Get the reviews written by this user.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
