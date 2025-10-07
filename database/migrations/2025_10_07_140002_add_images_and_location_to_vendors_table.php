<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->foreignId('region_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            $table->foreignId('district_id')->nullable()->after('region_id')->constrained()->nullOnDelete();
            $table->foreignId('town_id')->nullable()->after('district_id')->constrained()->nullOnDelete();
            $table->string('profile_photo')->nullable()->after('description');
            $table->json('work_samples')->nullable()->after('profile_photo');
            $table->json('social_links')->nullable()->after('work_samples');
            
            $table->index('region_id');
            $table->index('town_id');
        });
    }

    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropForeign(['region_id']);
            $table->dropForeign(['district_id']);
            $table->dropForeign(['town_id']);
            $table->dropColumn(['region_id', 'district_id', 'town_id', 'profile_photo', 'work_samples', 'social_links']);
        });
    }
};
