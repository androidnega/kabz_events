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
        // Add name change tracking to users table
        Schema::table('users', function (Blueprint $table) {
            $table->integer('name_changes_count')->default(0)->after('name');
            $table->timestamp('last_name_change_at')->nullable()->after('name_changes_count');
            $table->string('profile_photo')->nullable()->after('email_verified_at');
        });

        // Add business name change tracking to vendors table
        Schema::table('vendors', function (Blueprint $table) {
            $table->integer('business_name_changes_count')->default(0)->after('business_name');
            $table->timestamp('last_business_name_change_at')->nullable()->after('business_name_changes_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['name_changes_count', 'last_name_change_at', 'profile_photo']);
        });

        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn(['business_name_changes_count', 'last_business_name_change_at']);
        });
    }
};
