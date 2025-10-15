<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use Illuminate\Http\Request;

class AdminSettingsController extends Controller
{
    /**
     * Show settings page
     */
    public function index()
    {
        $settings = AdminSetting::query()->orderBy('group')->orderBy('key')->get()->groupBy('group');
        
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
        ]);

        foreach ($validated['settings'] as $key => $value) {
            $setting = AdminSetting::where('key', $key)->first();
            if ($setting) {
                $setting->update(['value' => $value]);
            }
        }

        return back()->with('success', 'Settings updated successfully');
    }

    /**
     * Show subscription settings
     */
    public function subscriptionSettings()
    {
        $settings = AdminSetting::query()->where('group', 'subscriptions')->get();
        
        return view('admin.settings.subscriptions', compact('settings'));
    }

    /**
     * Update subscription settings
     */
    public function updateSubscriptionSettings(Request $request)
    {
        $validated = $request->validate([
            'subscription_auto_approval_enabled' => 'nullable|boolean',
            'subscription_auto_approval_hours' => 'required|integer|min:1|max:168',
            'featured_ad_auto_approval_enabled' => 'nullable|boolean',
            'featured_ad_auto_approval_hours' => 'required|integer|min:1|max:168',
        ]);

        AdminSetting::set('subscription_auto_approval_enabled', $validated['subscription_auto_approval_enabled'] ?? false, 'boolean');
        AdminSetting::set('subscription_auto_approval_hours', $validated['subscription_auto_approval_hours'], 'integer');
        AdminSetting::set('featured_ad_auto_approval_enabled', $validated['featured_ad_auto_approval_enabled'] ?? false, 'boolean');
        AdminSetting::set('featured_ad_auto_approval_hours', $validated['featured_ad_auto_approval_hours'], 'integer');

        return back()->with('success', 'Auto-approval settings updated successfully');
    }

    /**
     * Show payment settings
     */
    public function paymentSettings()
    {
        $settings = AdminSetting::query()->where('group', 'payments')->get();
        
        return view('admin.settings.payments', compact('settings'));
    }

    /**
     * Update payment settings
     */
    public function updatePaymentSettings(Request $request)
    {
        $validated = $request->validate([
            'paystack_public_key' => 'nullable|string',
            'paystack_secret_key' => 'nullable|string',
        ]);

        if (isset($validated['paystack_public_key'])) {
            AdminSetting::set('paystack_public_key', $validated['paystack_public_key'], 'string', 'Paystack Public Key', 'payments');
        }

        if (isset($validated['paystack_secret_key'])) {
            AdminSetting::set('paystack_secret_key', $validated['paystack_secret_key'], 'string', 'Paystack Secret Key', 'payments');
        }

        return back()->with('success', 'Payment settings updated successfully');
    }
}
