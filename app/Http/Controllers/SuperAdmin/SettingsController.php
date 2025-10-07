<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\SettingsService;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Display system settings
     */
    public function index()
    {
        $paystack = SettingsService::getByGroup('paystack');
        $sms = SettingsService::getByGroup('sms');
        $storage = SettingsService::getByGroup('storage');
        $system = SettingsService::getByGroup('system');
        $backup = SettingsService::getByGroup('backup');

        return view('superadmin.settings.index', compact('paystack', 'sms', 'storage', 'system', 'backup'));
    }

    /**
     * Update system settings
     */
    public function update(Request $request)
    {
        foreach ($request->except('_token') as $key => $value) {
            // Determine group from key prefix
            $group = explode('_', $key)[0];
            
            // Determine type
            $type = 'string';
            if (in_array($key, ['paystack_enabled', 'sms_enabled'])) {
                $type = 'boolean';
                $value = $request->has($key) ? '1' : '0';
            } elseif (in_array($key, ['backup_retention_days'])) {
                $type = 'number';
            }
            
            SettingsService::set($key, $value, $type, $group);
        }

        SettingsService::clearCache();

        return back()->with('success', 'Settings updated successfully! 🇬🇭');
    }
}
