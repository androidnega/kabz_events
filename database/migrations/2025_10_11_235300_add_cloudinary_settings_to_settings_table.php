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
        Schema::table('settings', function (Blueprint $table) {
            $table->string('cloudinary_cloud_name')->nullable()->after('google_client_secret');
            $table->string('cloudinary_api_key')->nullable()->after('cloudinary_cloud_name');
            $table->string('cloudinary_api_secret')->nullable()->after('cloudinary_api_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['cloudinary_cloud_name', 'cloudinary_api_key', 'cloudinary_api_secret']);
        });
    }
};

