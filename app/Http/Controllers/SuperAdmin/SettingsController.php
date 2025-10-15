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

    public function testSmtp(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        // Set a custom timeout for this operation
        set_time_limit(120);

        try {
            // First, verify SMTP settings are configured
            $smtpHost = SettingsService::get('smtp_host');
            $smtpPort = SettingsService::get('smtp_port');
            $smtpUsername = SettingsService::get('smtp_username');
            
            if (empty($smtpHost) || empty($smtpPort)) {
                return back()->with('error', '❌ SMTP settings not configured. Please configure SMTP host and port first.');
            }

            // Configure timeout for Swift Mailer
            $originalTimeout = ini_get('default_socket_timeout');
            ini_set('default_socket_timeout', 30);

            \Illuminate\Support\Facades\Mail::raw(
                "This is a test email from KABZS EVENT.\n\nIf you received this, your SMTP configuration is working correctly!\n\nSent at: " . now()->format('Y-m-d H:i:s'),
                function ($message) use ($request) {
                    $message->to($request->test_email)
                            ->subject('Test Email - KABZS EVENT');
                }
            );

            // Restore original timeout
            ini_set('default_socket_timeout', $originalTimeout);

            return back()->with('success', '✅ Test email sent successfully to ' . $request->test_email . '! Please check your inbox (and spam folder).');
            
        } catch (\Swift_TransportException $e) {
            return back()->with('error', '❌ SMTP Connection Error: ' . $this->getSmtpErrorMessage($e->getMessage()));
        } catch (\Symfony\Component\Mailer\Exception\TransportException $e) {
            return back()->with('error', '❌ SMTP Connection Error: ' . $this->getSmtpErrorMessage($e->getMessage()));
        } catch (\Exception $e) {
            \Log::error('SMTP Test Failed: ' . $e->getMessage());
            return back()->with('error', '❌ Failed to send test email: ' . $this->getSmtpErrorMessage($e->getMessage()));
        } finally {
            // Restore original timeout if it was changed
            if (isset($originalTimeout)) {
                ini_set('default_socket_timeout', $originalTimeout);
            }
        }
    }

    /**
     * Get user-friendly SMTP error message
     */
    private function getSmtpErrorMessage($error)
    {
        if (str_contains($error, 'timed out') || str_contains($error, 'Connection timeout')) {
            return 'Connection timeout. Please check: 1) SMTP host is correct (mail.kabzevents.com), 2) Port is correct (465 for SSL, 587 for TLS), 3) Firewall is not blocking SMTP ports, 4) Your hosting provider allows outgoing SMTP connections.';
        }
        
        if (str_contains($error, 'Authentication failed') || str_contains($error, '535')) {
            return 'Authentication failed. Please verify your SMTP username and password are correct.';
        }
        
        if (str_contains($error, 'Could not connect') || str_contains($error, 'Connection refused')) {
            return 'Cannot connect to SMTP server. Please verify: 1) SMTP host is correct, 2) Port matches encryption (465=SSL, 587=TLS), 3) Server is accessible from your hosting.';
        }

        if (str_contains($error, 'certificate') || str_contains($error, 'SSL')) {
            return 'SSL/TLS certificate error. Try changing encryption method: If using SSL (port 465), try TLS (port 587), or vice versa.';
        }

        return substr($error, 0, 200); // Limit error message length
    }

    // ============================================================
    // Appearance / Theme Configuration
    // ============================================================
    
    public function appearance()
    {
        $settings = SettingsService::getByGroup('appearance');
        return view('superadmin.settings.appearance', compact('settings'));
    }

    public function updateAppearance(Request $request)
    {
        $request->validate([
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string|max:255',
            'hero_bg_type' => 'required|in:gradient,image',
            'hero_bg_image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'accent_color' => 'nullable|string|max:7',
        ]);

        // Handle hero background image upload
        if ($request->hasFile('hero_bg_image')) {
            $image = $request->file('hero_bg_image');
            $filename = 'hero-bg-' . time() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('public/appearance', $filename);
            SettingsService::set('hero_bg_image', 'appearance/' . $filename, 'string', 'appearance');
        }

        SettingsService::set('hero_title', $request->hero_title, 'string', 'appearance');
        SettingsService::set('hero_subtitle', $request->hero_subtitle, 'string', 'appearance');
        SettingsService::set('hero_bg_type', $request->hero_bg_type, 'string', 'appearance');
        SettingsService::set('primary_color', $request->primary_color, 'string', 'appearance');
        SettingsService::set('secondary_color', $request->secondary_color, 'string', 'appearance');
        SettingsService::set('accent_color', $request->accent_color, 'string', 'appearance');
        
        SettingsService::clearCache();

        return back()->with('success', '🎨 Appearance settings updated successfully!');
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
