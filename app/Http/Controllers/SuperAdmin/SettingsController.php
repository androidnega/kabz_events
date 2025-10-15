<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\SettingsService;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Display system settings overview
     */
    public function index()
    {
        $paystack = SettingsService::getByGroup('paystack');
        $sms = SettingsService::getByGroup('sms');
        $storage = SettingsService::getByGroup('storage');
        $system = SettingsService::getByGroup('system');
        $smtp = SettingsService::getByGroup('smtp');

        return view('superadmin.settings.index', compact('paystack', 'sms', 'storage', 'system', 'smtp'));
    }

    // ============================================================
    // Paystack Configuration
    // ============================================================
    
    public function paystack()
    {
        $settings = SettingsService::getByGroup('paystack');
        return view('superadmin.settings.paystack', compact('settings'));
    }

    public function updatePaystack(Request $request)
    {
        $request->validate([
            'paystack_public_key' => 'nullable|string',
            'paystack_secret_key' => 'nullable|string',
        ]);

        SettingsService::set('paystack_public_key', $request->paystack_public_key, 'string', 'paystack');
        SettingsService::set('paystack_secret_key', $request->paystack_secret_key, 'string', 'paystack');
        SettingsService::set('paystack_enabled', $request->has('paystack_enabled') ? '1' : '0', 'boolean', 'paystack');
        
        SettingsService::clearCache();

        return back()->with('success', '💳 Paystack configuration updated successfully!');
    }

    // ============================================================
    // Cloudinary Configuration
    // ============================================================
    
    public function cloudinary()
    {
        $settings = SettingsService::getByGroup('storage');
        return view('superadmin.settings.cloudinary', compact('settings'));
    }

    public function updateCloudinary(Request $request)
    {
        $request->validate([
            'cloud_storage' => 'required|in:local,cloudinary',
            'cloudinary_cloud_name' => 'nullable|string',
            'cloudinary_api_key' => 'nullable|string',
            'cloudinary_api_secret' => 'nullable|string',
        ]);

        SettingsService::set('cloud_storage', $request->cloud_storage, 'string', 'storage');
        SettingsService::set('cloudinary_cloud_name', $request->cloudinary_cloud_name, 'string', 'storage');
        SettingsService::set('cloudinary_api_key', $request->cloudinary_api_key, 'string', 'storage');
        SettingsService::set('cloudinary_api_secret', $request->cloudinary_api_secret, 'string', 'storage');
        
        SettingsService::clearCache();

        return back()->with('success', '☁️ Cloudinary configuration updated successfully!');
    }

    // ============================================================
    // Arkasel SMS Configuration
    // ============================================================
    
    public function sms()
    {
        $settings = SettingsService::getByGroup('sms');
        return view('superadmin.settings.sms', compact('settings'));
    }

    public function updateSms(Request $request)
    {
        $request->validate([
            'sms_api_key' => 'nullable|string',
            'sms_api_secret' => 'nullable|string',
            'sms_sender_id' => 'nullable|string|max:11',
        ]);

        SettingsService::set('sms_provider', 'arkasel', 'string', 'sms');
        SettingsService::set('sms_api_key', $request->sms_api_key, 'string', 'sms');
        SettingsService::set('sms_api_secret', $request->sms_api_secret, 'string', 'sms');
        SettingsService::set('sms_sender_id', $request->sms_sender_id, 'string', 'sms');
        SettingsService::set('sms_enabled', $request->has('sms_enabled') ? '1' : '0', 'boolean', 'sms');
        
        SettingsService::clearCache();

        return back()->with('success', '📱 Arkasel SMS configuration updated successfully!');
    }

    // ============================================================
    // SMTP Email Configuration
    // ============================================================
    
    public function smtp()
    {
        $settings = SettingsService::getByGroup('smtp');
        return view('superadmin.settings.smtp', compact('settings'));
    }

    public function updateSmtp(Request $request)
    {
        $request->validate([
            'smtp_host' => 'nullable|string',
            'smtp_port' => 'nullable|integer',
            'smtp_username' => 'nullable|string',
            'smtp_password' => 'nullable|string',
            'smtp_encryption' => 'nullable|in:tls,ssl',
            'smtp_from_address' => 'nullable|email',
            'smtp_from_name' => 'nullable|string',
        ]);

        SettingsService::set('smtp_host', $request->smtp_host, 'string', 'smtp');
        SettingsService::set('smtp_port', $request->smtp_port, 'number', 'smtp');
        SettingsService::set('smtp_username', $request->smtp_username, 'string', 'smtp');
        SettingsService::set('smtp_password', $request->smtp_password, 'string', 'smtp');
        SettingsService::set('smtp_encryption', $request->smtp_encryption, 'string', 'smtp');
        SettingsService::set('smtp_from_address', $request->smtp_from_address, 'string', 'smtp');
        SettingsService::set('smtp_from_name', $request->smtp_from_name, 'string', 'smtp');
        
        SettingsService::clearCache();

        return back()->with('success', '📧 SMTP configuration updated successfully!');
    }

    // ============================================================
    // System Configuration
    // ============================================================
    
    public function system()
    {
        $settings = SettingsService::getByGroup('system');
        return view('superadmin.settings.system', compact('settings'));
    }

    public function updateSystem(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'site_email' => 'required|email',
            'site_phone' => 'nullable|string',
            'default_currency' => 'required|string|max:3',
            'currency_symbol' => 'required|string|max:5',
            'timezone' => 'required|string',
        ]);

        SettingsService::set('site_name', $request->site_name, 'string', 'system');
        SettingsService::set('site_email', $request->site_email, 'string', 'system');
        SettingsService::set('site_phone', $request->site_phone, 'string', 'system');
        SettingsService::set('default_currency', $request->default_currency, 'string', 'system');
        SettingsService::set('currency_symbol', $request->currency_symbol, 'string', 'system');
        SettingsService::set('timezone', $request->timezone, 'string', 'system');
        
        SettingsService::clearCache();

        return back()->with('success', '⚙️ System configuration updated successfully!');
    }

    // ============================================================
    // Site Mode / Maintenance Configuration
    // ============================================================
    
    public function maintenance()
    {
        $settings = SettingsService::getByGroup('maintenance');
        return view('superadmin.settings.maintenance', compact('settings'));
    }

    public function updateMaintenance(Request $request)
    {
        $request->validate([
            'site_mode' => 'required|in:live,maintenance,coming_soon,update',
            'maintenance_message' => 'nullable|string',
            'coming_soon_message' => 'nullable|string',
            'update_message' => 'nullable|string',
            'maintenance_end_time' => 'nullable|date',
        ]);

        SettingsService::set('site_mode', $request->site_mode, 'string', 'maintenance');
        SettingsService::set('site_mode_enabled', $request->has('site_mode_enabled') ? '1' : '0', 'boolean', 'maintenance');
        SettingsService::set('maintenance_message', $request->maintenance_message, 'string', 'maintenance');
        SettingsService::set('coming_soon_message', $request->coming_soon_message, 'string', 'maintenance');
        SettingsService::set('update_message', $request->update_message, 'string', 'maintenance');
        SettingsService::set('maintenance_end_time', $request->maintenance_end_time, 'string', 'maintenance');
        
        SettingsService::clearCache();

        $modeLabels = [
            'live' => '🟢 Live',
            'maintenance' => '🔧 Maintenance',
            'coming_soon' => '🚀 Coming Soon',
            'update' => '⬆️ Update',
        ];

        $modeLabel = $modeLabels[$request->site_mode] ?? 'Site';
        $status = $request->has('site_mode_enabled') ? 'enabled' : 'disabled';

        return back()->with('success', "{$modeLabel} mode {$status} successfully!");
    }
}
