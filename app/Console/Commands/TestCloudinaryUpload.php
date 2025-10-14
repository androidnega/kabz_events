<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Services\SettingsService;
use Cloudinary\Cloudinary;
use Cloudinary\Api\Upload\UploadApi;

class TestCloudinaryUpload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cloudinary:test {--count=3 : Number of images to upload}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Cloudinary upload by uploading sample vendor images';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing Cloudinary Upload...');
        $this->newLine();

        // Step 1: Check Cloudinary configuration
        $this->info('📋 Step 1: Checking Cloudinary Configuration');
        $cloudName = SettingsService::get('cloudinary_cloud_name');
        $apiKey = SettingsService::get('cloudinary_api_key');
        $apiSecret = SettingsService::get('cloudinary_api_secret');
        $storageProvider = SettingsService::get('cloud_storage', 'local');

        if (!$cloudName || !$apiKey || !$apiSecret) {
            $this->error('❌ Cloudinary credentials not configured!');
            $this->info('Please configure Cloudinary in: /dashboard/settings/cloudinary');
            return 1;
        }

        $this->line("   ✓ Cloud Name: {$cloudName}");
        $this->line("   ✓ API Key: {$apiKey}");
        $this->line("   ✓ API Secret: " . str_repeat('*', strlen($apiSecret)));
        $this->line("   ✓ Storage Provider: {$storageProvider}");
        $this->newLine();

        // Step 2: Initialize Cloudinary
        $this->info('📋 Step 2: Initializing Cloudinary');
        try {
            $cloudinary = new Cloudinary([
                'cloud' => [
                    'cloud_name' => $cloudName,
                    'api_key' => $apiKey,
                    'api_secret' => $apiSecret
                ]
            ]);
            $this->line('   ✓ Cloudinary initialized successfully');
        } catch (\Exception $e) {
            $this->error('   ❌ Failed to initialize Cloudinary: ' . $e->getMessage());
            return 1;
        }
        $this->newLine();

        // Step 3: Find test images
        $this->info('📋 Step 3: Finding Test Images');
        $count = (int) $this->option('count');
        $publicPath = storage_path('app/public');
        
        $testImages = [];
        $patterns = ['vendors/profiles/*.jpg', 'vendors/sample_work/*.jpg', 'vendors/samples/*.jpg'];
        
        foreach ($patterns as $pattern) {
            $files = glob($publicPath . '/' . $pattern);
            if ($files) {
                $testImages = array_merge($testImages, array_slice($files, 0, $count - count($testImages)));
                if (count($testImages) >= $count) break;
            }
        }

        if (empty($testImages)) {
            $this->warn('⚠️  No test images found in storage/app/public/vendors/');
            $this->info('Upload some vendor images first or create sample images.');
            return 1;
        }

        $this->line("   ✓ Found " . count($testImages) . " test images");
        foreach ($testImages as $i => $image) {
            $filename = basename($image);
            $size = number_format(filesize($image) / 1024, 2);
            $this->line("   " . ($i + 1) . ". {$filename} ({$size} KB)");
        }
        $this->newLine();

        // Step 4: Upload to Cloudinary
        $this->info('📋 Step 4: Uploading to Cloudinary');
        $bar = $this->output->createProgressBar(count($testImages));
        $bar->start();

        $results = [];
        foreach ($testImages as $imagePath) {
            try {
                $response = $cloudinary->uploadApi()->upload($imagePath, [
                    'folder' => 'vendor_sample_work',
                    'resource_type' => 'image',
                    'use_filename' => true,
                    'unique_filename' => true
                ]);

                $results[] = [
                    'success' => true,
                    'local' => basename($imagePath),
                    'url' => $response['secure_url'],
                    'public_id' => $response['public_id'],
                    'size' => $response['bytes']
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'success' => false,
                    'local' => basename($imagePath),
                    'error' => $e->getMessage()
                ];
            }
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine(2);

        // Step 5: Display Results
        $this->info('📋 Step 5: Upload Results');
        $successful = 0;
        $failed = 0;

        foreach ($results as $result) {
            if ($result['success']) {
                $successful++;
                $this->line("   ✅ {$result['local']}");
                $this->line("      URL: {$result['url']}");
                $this->line("      ID: {$result['public_id']}");
            } else {
                $failed++;
                $this->line("   ❌ {$result['local']}");
                $this->line("      Error: {$result['error']}");
            }
            $this->newLine();
        }

        // Summary
        $this->newLine();
        $totalImages = count($testImages);
        if ($successful > 0) {
            $this->info("✅ SUCCESS! Uploaded {$successful}/{$totalImages} images to Cloudinary");
            $this->info('🔗 View your images at: https://cloudinary.com/console/media_library');
            $this->newLine();
            $this->info('Next steps:');
            $this->line('   1. Log into your Cloudinary dashboard');
            $this->line('   2. Check the "vendor_sample_work" folder');
            $this->line('   3. Your uploaded images should be visible there');
            $this->line('   4. You can now register as a vendor and upload images - they will go to Cloudinary!');
        }

        if ($failed > 0) {
            $this->warn("⚠️  {$failed} images failed to upload");
        }

        return 0;
    }
}
