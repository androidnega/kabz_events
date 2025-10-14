<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SettingsService;
use Cloudinary\Cloudinary;

class CloudinarySetupFolders extends Command
{
    protected $signature = 'cloudinary:setup-folders';
    protected $description = 'Create folder structure in Cloudinary for Kabz Events';

    public function handle()
    {
        $this->info('🗂️  Setting up Cloudinary folder structure...');
        $this->newLine();

        // Get credentials
        $cloudName = SettingsService::get('cloudinary_cloud_name');
        $apiKey = SettingsService::get('cloudinary_api_key');
        $apiSecret = SettingsService::get('cloudinary_api_secret');

        if (!$cloudName || !$apiKey || !$apiSecret) {
            $this->error('❌ Cloudinary not configured!');
            $this->info('Please configure Cloudinary at: /dashboard/settings/cloudinary');
            return 1;
        }

        $this->line("🔑 Connected to: {$cloudName}");
        $this->newLine();

        try {
            $cloudinary = new Cloudinary([
                'cloud' => [
                    'cloud_name' => $cloudName,
                    'api_key' => $apiKey,
                    'api_secret' => $apiSecret
                ]
            ]);

            // Define the folders to create
            $folders = [
                'vendor_sample_work' => 'Vendor sample work images and portfolios',
                'profile_photos' => 'Vendor and user profile photos',
                'promo_videos' => 'Promotional videos for vendors',
                'Ghana_card_verifications' => 'Ghana Card and verification documents'
            ];

            $this->info('📂 Creating folders in Cloudinary:');
            $this->line(str_repeat('─', 60));
            $this->newLine();

            $created = 0;
            $failed = 0;

            foreach ($folders as $folder => $description) {
                try {
                    // Create a placeholder file to establish the folder
                    // We'll upload a tiny transparent 1x1 PNG
                    $placeholderData = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
                    
                    // Create temporary file
                    $tempFile = tempnam(sys_get_temp_dir(), 'cloudinary_');
                    file_put_contents($tempFile, $placeholderData);

                    // Upload to Cloudinary to create the folder
                    $response = $cloudinary->uploadApi()->upload($tempFile, [
                        'folder' => $folder,
                        'public_id' => '.folder_placeholder',
                        'resource_type' => 'image',
                        'type' => 'upload'
                    ]);

                    // Delete temp file
                    unlink($tempFile);

                    $this->line("   ✅ {$folder}/");
                    $this->line("      {$description}");
                    $this->newLine();
                    $created++;

                } catch (\Exception $e) {
                    $this->line("   ❌ {$folder}/");
                    $this->line("      Error: " . $e->getMessage());
                    $this->newLine();
                    $failed++;
                }
            }

            // Summary
            $this->newLine();
            $this->line(str_repeat('─', 60));
            $this->info("✅ Created: {$created} folders");
            
            if ($failed > 0) {
                $this->warn("⚠️  Failed: {$failed} folders");
            }

            $this->newLine();
            $this->info('🎯 Next Steps:');
            $this->line('   1. View folders at: https://cloudinary.com/console/media_library');
            $this->line('   2. Upload images - they will use these folders');
            $this->line('   3. Check folder structure: php artisan cloudinary:list');
            $this->newLine();
            $this->info('📁 Folder Structure:');
            $this->line("   ├── vendor_sample_work/     (Sample work images)");
            $this->line("   ├── profile_photos/         (Profile photos)");
            $this->line("   ├── promo_videos/           (Promotional videos)");
            $this->line("   └── Ghana_card_verifications/ (Verification documents)");

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }
    }
}
