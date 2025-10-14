<?php

namespace App\Console\Commands;

use App\Services\SettingsService;
use Cloudinary\Cloudinary;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CloudinaryRemoveDuplicates extends Command
{
    protected $signature = 'cloudinary:remove-duplicates {--folder= : Specific folder to check} {--dry-run : Show duplicates without deleting}';
    protected $description = 'Find and remove duplicate images from Cloudinary';

    public function handle()
    {
        $this->info('🔍 Scanning for duplicate images in Cloudinary...');
        $this->newLine();

        // Initialize Cloudinary
        $cloudName = SettingsService::get('cloudinary_cloud_name');
        $apiKey = SettingsService::get('cloudinary_api_key');
        $apiSecret = SettingsService::get('cloudinary_api_secret');

        if (!$cloudName || !$apiKey || !$apiSecret) {
            $this->error('❌ Cloudinary not configured!');
            return 1;
        }

        $cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => $cloudName,
                'api_key' => $apiKey,
                'api_secret' => $apiSecret
            ]
        ]);

        $folders = ['vendor_sample_work', 'profile_photos', 'promo_videos', 'Ghana_card_verifications'];
        $specificFolder = $this->option('folder');
        
        if ($specificFolder) {
            $folders = [$specificFolder];
        }

        $totalDuplicates = 0;
        $totalDeleted = 0;
        $isDryRun = $this->option('dry-run');

        foreach ($folders as $folder) {
            $this->info("📁 Checking folder: {$folder}");
            $this->line(str_repeat('─', 60));

            try {
                $response = $cloudinary->adminApi()->assets([
                    'type' => 'upload',
                    'prefix' => $folder,
                    'max_results' => 500
                ]);

                $images = $response['resources'] ?? [];
                $count = count($images);
                $this->line("   Found {$count} files");

                // Group by file size to find potential duplicates
                $grouped = [];
                foreach ($images as $image) {
                    $size = $image['bytes'];
                    if (!isset($grouped[$size])) {
                        $grouped[$size] = [];
                    }
                    $grouped[$size][] = $image;
                }

                // Find duplicates
                $duplicates = [];
                foreach ($grouped as $size => $group) {
                    if (count($group) > 1) {
                        // Check if they have similar names
                        $names = [];
                        foreach ($group as $img) {
                            $baseName = preg_replace('/_[a-z0-9]{6}$/', '', basename($img['public_id']));
                            if (!isset($names[$baseName])) {
                                $names[$baseName] = [];
                            }
                            $names[$baseName][] = $img;
                        }

                        foreach ($names as $baseName => $imgs) {
                            if (count($imgs) > 1) {
                                // Keep the newest, mark others for deletion
                                usort($imgs, function($a, $b) {
                                    return strtotime($b['created_at']) - strtotime($a['created_at']);
                                });

                                // Skip first (newest), mark rest as duplicates
                                for ($i = 1; $i < count($imgs); $i++) {
                                    $duplicates[] = $imgs[$i];
                                }
                            }
                        }
                    }
                }

                if (count($duplicates) > 0) {
                    $this->warn("   ⚠️  Found " . count($duplicates) . " duplicate(s)");
                    $totalDuplicates += count($duplicates);

                    foreach ($duplicates as $dup) {
                        $this->line("      • " . basename($dup['public_id']) . " (" . number_format($dup['bytes']/1024, 1) . " KB)");
                        
                        if (!$isDryRun) {
                            try {
                                $cloudinary->uploadApi()->destroy($dup['public_id'], [
                                    'resource_type' => $dup['resource_type']
                                ]);
                                $this->line("        ✓ Deleted");
                                $totalDeleted++;
                            } catch (\Exception $e) {
                                $this->line("        ✗ Failed: " . $e->getMessage());
                            }
                        }
                    }
                } else {
                    $this->line("   ✓ No duplicates found");
                }

            } catch (\Exception $e) {
                $this->error("   ✗ Error: " . $e->getMessage());
            }

            $this->newLine();
        }

        // Summary
        $this->newLine();
        $this->line(str_repeat('═', 60));
        
        if ($isDryRun) {
            $this->info("🔍 DRY RUN COMPLETE");
            $this->line("   Found {$totalDuplicates} duplicate(s)");
            $this->newLine();
            $this->info("💡 Run without --dry-run to delete duplicates:");
            $this->line("   php artisan cloudinary:remove-duplicates");
        } else {
            $this->info("✅ CLEANUP COMPLETE");
            $this->line("   Deleted {$totalDeleted} duplicate(s)");
        }

        return 0;
    }
}
