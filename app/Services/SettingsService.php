<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    /**
     * Get a setting value
     */
    public static function get(string $key, $default = null)
    {
        return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
            $setting = SystemSetting::where('key', $key)->first();
            
            if (!$setting) {
                return $default;
            }
            
            // Handle JSON type
            if ($setting->type === 'json') {
                return json_decode($setting->value, true);
            }
            
            // Handle boolean type
            if ($setting->type === 'boolean') {
                return filter_var($setting->value, FILTER_VALIDATE_BOOLEAN);
            }
            
            // Handle number type
            if ($setting->type === 'number') {
                return is_numeric($setting->value) ? (float) $setting->value : $default;
            }
            
            return $setting->value;
        });
    }
    
    /**
     * Set a setting value
     */
    public static function set(string $key, $value, string $type = 'string', string $group = null): void
    {
        // Convert value based on type
        if ($type === 'json') {
            $value = json_encode($value);
        } elseif ($type === 'boolean') {
            $value = $value ? '1' : '0';
        }
        
        SystemSetting::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type, 'group' => $group]
        );
        
        Cache::forget("setting_{$key}");
    }
    
    /**
     * Get all settings by group
     */
    public static function getByGroup(string $group): array
    {
        return Cache::remember("settings_group_{$group}", 3600, function () use ($group) {
            return SystemSetting::where('group', $group)->pluck('value', 'key')->toArray();
        });
    }
    
    /**
     * Clear all settings cache
     */
    public static function clearCache(): void
    {
        Cache::tags(['settings'])->flush();
    }
}

