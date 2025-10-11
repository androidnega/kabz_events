<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('verification_requests', function (Blueprint $table) {
            // Page 1: Business Information
            $table->string('business_category')->nullable()->after('vendor_id');
            $table->string('business_registration_number')->nullable()->after('business_category');
            $table->text('business_description')->nullable()->after('business_registration_number');
            $table->integer('years_in_operation')->nullable()->after('business_description');
            $table->string('business_location')->nullable()->after('years_in_operation');
            $table->string('business_logo_path')->nullable()->after('business_location');
            
            // Page 2: Contact and Identity
            $table->string('contact_full_name')->nullable()->after('business_logo_path');
            $table->string('contact_role')->nullable()->after('contact_full_name');
            $table->string('contact_phone')->nullable()->after('contact_role');
            $table->string('contact_email')->nullable()->after('contact_phone');
            $table->string('national_id_type')->nullable()->after('contact_email');
            $table->string('national_id_number')->nullable()->after('national_id_type');
            $table->string('profile_picture_path')->nullable()->after('id_document_path');
            
            // Page 3: Verification Evidence
            $table->string('website_url')->nullable()->after('social_links');
            $table->json('proof_of_events')->nullable()->after('website_url'); // multiple files
            $table->string('reference_letter_path')->nullable()->after('proof_of_events');
            $table->text('verification_reason')->nullable()->after('reference_letter_path');
            $table->boolean('terms_agreed')->default(false)->after('verification_reason');
            $table->boolean('details_confirmed')->default(false)->after('terms_agreed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('verification_requests', function (Blueprint $table) {
            $table->dropColumn([
                'business_category',
                'business_registration_number',
                'business_description',
                'years_in_operation',
                'business_location',
                'business_logo_path',
                'contact_full_name',
                'contact_role',
                'contact_phone',
                'contact_email',
                'national_id_type',
                'national_id_number',
                'profile_picture_path',
                'website_url',
                'proof_of_events',
                'reference_letter_path',
                'verification_reason',
                'terms_agreed',
                'details_confirmed',
            ]);
        });
    }
};
