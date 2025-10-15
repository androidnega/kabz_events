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
        Schema::create('admin_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, boolean, integer, json
            $table->text('description')->nullable();
            $table->string('group')->default('general'); // general, payments, subscriptions, notifications
            $table->timestamps();
            
            $table->index('key');
            $table->index('group');
        });
        
        // Insert default settings
        DB::table('admin_settings')->insert([
            [
                'key' => 'subscription_auto_approval_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Enable automatic approval of subscriptions after 24 hours',
                'group' => 'subscriptions',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'subscription_auto_approval_hours',
                'value' => '24',
                'type' => 'integer',
                'description' => 'Hours to wait before auto-approving subscriptions',
                'group' => 'subscriptions',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'featured_ad_auto_approval_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Enable automatic approval of featured ads after 24 hours',
                'group' => 'subscriptions',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'featured_ad_auto_approval_hours',
                'value' => '24',
                'type' => 'integer',
                'description' => 'Hours to wait before auto-approving featured ads',
                'group' => 'subscriptions',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'paystack_public_key',
                'value' => '',
                'type' => 'string',
                'description' => 'Paystack Public Key',
                'group' => 'payments',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'paystack_secret_key',
                'value' => '',
                'type' => 'string',
                'description' => 'Paystack Secret Key',
                'group' => 'payments',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_settings');
    }
};
