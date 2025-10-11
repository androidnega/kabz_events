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
        Schema::table('users', function (Blueprint $table) {
            // User's current location for proximity-based recommendations
            $table->decimal('latitude', 10, 7)->nullable()->after('password');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->string('location_name')->nullable()->after('longitude'); // e.g., "Accra, Greater Accra"
            $table->timestamp('location_updated_at')->nullable()->after('location_name');
            
            // User preferences for personalized search
            $table->json('preferred_categories')->nullable()->after('location_updated_at'); // Array of category IDs
            $table->string('preferred_region')->nullable()->after('preferred_categories'); // Ghana region
            $table->decimal('search_radius_km', 8, 2)->default(50)->after('preferred_region'); // Default 50km radius
            
            // Search history metadata
            $table->integer('total_searches')->default(0)->after('search_radius_km');
            $table->integer('total_vendor_views')->default(0)->after('total_searches');
            
            // Indexes for performance
            $table->index(['latitude', 'longitude']);
            $table->index('preferred_region');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['latitude', 'longitude']);
            $table->dropIndex(['preferred_region']);
            
            $table->dropColumn([
                'latitude',
                'longitude',
                'location_name',
                'location_updated_at',
                'preferred_categories',
                'preferred_region',
                'search_radius_km',
                'total_searches',
                'total_vendor_views',
            ]);
        });
    }
};
