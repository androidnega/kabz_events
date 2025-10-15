<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VerificationRequest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vendor_id',
        // Page 1: Business Information
        'business_category',
        'business_registration_number',
        'business_description',
        'years_in_operation',
        'business_location',
        'business_logo_path',
        // Page 2: Contact and Identity
        'contact_full_name',
        'contact_role',
        'contact_phone',
        'contact_email',
        'national_id_type',
        'national_id_number',
        'id_document_path',
        'profile_picture_path',
        // Page 3: Verification Evidence
        'social_links',
        'website_url',
        'proof_of_events',
        'reference_letter_path',
        'verification_reason',
        'terms_agreed',
        'details_confirmed',
        // Status
        'status',
        'admin_note',
        'submitted_at',
        'decided_at',
        'decided_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'social_links' => 'array',
        'proof_of_events' => 'array',
        'terms_agreed' => 'boolean',
        'details_confirmed' => 'boolean',
        'submitted_at' => 'datetime',
        'decided_at' => 'datetime',
    ];

    /**
     * Get the vendor that owns the verification request.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the admin who decided on this request.
     */
    public function decidedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }
}
