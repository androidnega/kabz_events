<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SettingsService;
use Cloudinary\Cloudinary;

class CloudinaryList extends Command
{
    protected $signature = 'cloudinary:list {--folder=kabz_test : Folder to list images from}';
    protected $description = 'List images in your Cloudinary account';

    public function handle()
    {
        $this->info('📂 Listing Cloudinary Images...');
        $this->newLine();

        // Get credentials
        $cloudName = SettingsService::get('cloudinary_cloud_name');
        $apiKey = SettingsService::get('cloudinary_api_key');
        $apiSecret = SettingsService::get('cloudinary_api_secret');

        if (!$cloudName || !$apiKey || !$apiSecret) {
            $this->error('❌ Cloudinary not configured!');
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

            $folder = $this->option('folder');

            // List images in the specified folder
            $this->info("📁 Folder: {$folder}");
            $this->line(str_repeat('─', 60));
            
            $response = $cloudinary->adminApi()->assets([
                'type' => 'upload',
                'prefix' => $folder,
                'max_results' => 50
            ]);

            if (empty($response['resources'])) {
                $this->warn("   No images found in '{$folder}' folder");
                $this->newLine();
                $this->info("💡 Tip: Use --folder option to search other folders");
                $this->line("   Example: php artisan cloudinary:list --folder=samples");
                return 0;
            }

            $this->info("   Found " . count($response['resources']) . " images:");
            $this->newLine();

            foreach ($response['resources'] as $i => $resource) {
                $num = $i + 1;
                $filename = basename($resource['public_id']);
                $size = number_format($resource['bytes'] / 1024, 2);
                $created = date('Y-m-d H:i:s', strtotime($resource['created_at']));
                
                $this->line("   {$num}. {$filename}");
                $this->line("      Size: {$size} KB | Uploaded: {$created}");
                $this->line("      URL: {$resource['secure_url']}");
                $this->newLine();
            }

            // List root folders
            $this->newLine();
            $this->info('📂 Available Folders in Your Cloud:');
            $this->line(str_repeat('─', 60));
            
            $folders = $cloudinary->adminApi()->rootFolders();
            
            if (!empty($folders['folders'])) {
                foreach ($folders['folders'] as $folder) {
                    $this->line("   • {$folder['name']} ({$folder['path']})");
                }
            } else {
                $this->line("   No folders found");
            }
            
            $this->newLine();
            $this->info("✅ Total images in '{$this->option('folder')}': " . count($response['resources']));
            $this->line("🔗 View all at: https://cloudinary.com/console/media_library");

        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
