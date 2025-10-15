<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use App\Services\SettingsService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Configure SMTP settings dynamically from database
        try {
            $smtpHost = SettingsService::get('smtp_host');
            $smtpPort = SettingsService::get('smtp_port');
            $smtpUsername = SettingsService::get('smtp_username');
            $smtpPassword = SettingsService::get('smtp_password');
            $smtpEncryption = SettingsService::get('smtp_encryption', 'tls');
            $smtpFromAddress = SettingsService::get('smtp_from_address');
            $smtpFromName = SettingsService::get('smtp_from_name', config('app.name'));

            // Only configure if SMTP host is set
            if ($smtpHost) {
                Config::set('mail.mailers.smtp.host', $smtpHost);
                Config::set('mail.mailers.smtp.port', $smtpPort);
                Config::set('mail.mailers.smtp.username', $smtpUsername);
                Config::set('mail.mailers.smtp.password', $smtpPassword);
                Config::set('mail.mailers.smtp.encryption', $smtpEncryption);
                
                if ($smtpFromAddress) {
                    Config::set('mail.from.address', $smtpFromAddress);
                }
                
                if ($smtpFromName) {
                    Config::set('mail.from.name', $smtpFromName);
                }
            }
        } catch (\Exception $e) {
            // Silently fail if database is not yet configured
            // This prevents errors during installation or migration
        }
    }
}
