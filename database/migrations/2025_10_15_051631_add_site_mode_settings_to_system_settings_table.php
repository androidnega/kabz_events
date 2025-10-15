<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\SystemSetting;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add site mode settings to system_settings table
        $settings = [
            [
                'key' => 'site_mode',
                'value' => 'live',
                'type' => 'string',
                'group' => 'maintenance',
            ],
            [
                'key' => 'site_mode_enabled',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'maintenance',
            ],
            [
                'key' => 'maintenance_message',
                'value' => 'We are currently performing scheduled maintenance. We\'ll be back shortly!',
                'type' => 'string',
                'group' => 'maintenance',
            ],
            [
                'key' => 'coming_soon_message',
                'value' => 'Something amazing is coming soon! Stay tuned.',
                'type' => 'string',
                'group' => 'maintenance',
            ],
            [
                'key' => 'update_message',
                'value' => 'We are currently updating our system with exciting new features. Please check back soon!',
                'type' => 'string',
                'group' => 'maintenance',
            ],
            [
                'key' => 'maintenance_end_time',
                'value' => '',
                'type' => 'string',
                'group' => 'maintenance',
            ],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove site mode settings
        SystemSetting::whereIn('key', [
            'site_mode',
            'site_mode_enabled',
            'maintenance_message',
            'coming_soon_message',
            'update_message',
            'maintenance_end_time',
        ])->delete();
    }
};
