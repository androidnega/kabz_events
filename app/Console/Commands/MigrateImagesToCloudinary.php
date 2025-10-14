<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Vendor;
use App\Services\CloudinaryService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class MigrateImagesToCloudinary extends Command
{
    protected $signature = 'cloudinary:migrate {--type=all : Type to migrate: all, profiles, samples, videos}';
    protected $description = 'Migrate existing local images to Cloudinary';

    protected $cloudinaryService;
    protected $stats = [
        'profiles' => ['success' => 0, 'failed' => 0, 'skipped' => 0],
        'samples' => ['success' => 0, 'failed' => 0, 'skipped' => 0],
        'videos' => ['success' => 0, 'failed' => 0, 'skipped' => 0],
    ];

    public function handle()
    {
        $this->cloudinaryService = new CloudinaryService();

        if (!$this->cloudinaryService->isEnabled()) {
            $this->error('❌ Cloudinary is not enabled or not configured!');
            $this->info('Please configure Cloudinary at: /dashboard/settings/cloudinary');
            return 1;
        }

        $this->info('🚀 Starting migration to Cloudinary...');
        $this->newLine();

        $type = $this->option('type');

        if ($type === 'all' || $type === 'profiles') {
            $this->migrateProfilePhotos();
        }

        if ($type === 'all' || $type === 'samples') {
            $this->migrateSampleWork();
        }

        if ($type === 'all' || $type === 'videos') {
            $this->migrateVideos();
        }

        $this->displaySummary();

        return 0;
    }

    protected function migrateProfilePhotos()
    {
        $this->info('📸 Migrating Profile Photos...');
        $this->line(str_repeat('─', 60));
        $this->newLine();

        // Migrate vendor profile photos
        $vendors = Vendor::whereNotNull('profile_photo')
            ->where('profile_photo', 'not like', 'https://%')
            ->get();

        $this->line("Found {$vendors->count()} vendor profile photos to migrate");
        
        $bar = $this->output->createProgressBar($vendors->count());
        $bar->start();

        foreach ($vendors as $vendor) {
            try {
                $localPath = $vendor->profile_photo;
                
                // Check if file exists
                if (!Storage::disk('public')->exists($localPath)) {
                    $this->stats['profiles']['skipped']++;
                    $bar->advance();
                    continue;
                }

                // Get the full path
                $fullPath = Storage::disk('public')->path($localPath);
                
                // Create a temporary UploadedFile instance
                $file = new \Illuminate\Http\UploadedFile(
                    $fullPath,
                    basename($localPath),
                    Storage::disk('public')->mimeType($localPath),
                    null,
                    true
                );

                // Upload to Cloudinary
                $result = $this->cloudinaryService->uploadImage($file, 'profile_photos');

                if ($result['success'] && $result['provider'] === 'cloudinary') {
                    // Update vendor record
                    $vendor->profile_photo = $result['url'];
                    $vendor->save();

                    $this->stats['profiles']['success']++;
                    
                    // Optionally delete local file
                    // Storage::disk('public')->delete($localPath);
                } else {
                    $this->stats['profiles']['skipped']++;
                }

            } catch (\Exception $e) {
                Log::error('Profile migration failed: ' . $e->getMessage(), [
                    'vendor_id' => $vendor->id,
                    'photo' => $vendor->profile_photo
                ]);
                $this->stats['profiles']['failed']++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Migrate user profile photos
        $users = User::whereNotNull('profile_photo')
            ->where('profile_photo', 'not like', 'https://%')
            ->get();

        if ($users->count() > 0) {
            $this->line("Found {$users->count()} user profile photos to migrate");
            
            $userBar = $this->output->createProgressBar($users->count());
            $userBar->start();

            foreach ($users as $user) {
                try {
                    $localPath = $user->profile_photo;
                    
                    if (!Storage::disk('public')->exists($localPath)) {
                        $this->stats['profiles']['skipped']++;
                        $userBar->advance();
                        continue;
                    }

                    $fullPath = Storage::disk('public')->path($localPath);
                    
                    $file = new \Illuminate\Http\UploadedFile(
                        $fullPath,
                        basename($localPath),
                        Storage::disk('public')->mimeType($localPath),
                        null,
                        true
                    );

                    $result = $this->cloudinaryService->uploadImage($file, 'profile_photos');

                    if ($result['success'] && $result['provider'] === 'cloudinary') {
                        $user->profile_photo = $result['url'];
                        $user->save();
                        $this->stats['profiles']['success']++;
                    } else {
                        $this->stats['profiles']['skipped']++;
                    }

                } catch (\Exception $e) {
                    Log::error('User profile migration failed: ' . $e->getMessage());
                    $this->stats['profiles']['failed']++;
                }

                $userBar->advance();
            }

            $userBar->finish();
            $this->newLine(2);
        }
    }

    protected function migrateSampleWork()
    {
        $this->info('🖼️  Migrating Sample Work Images...');
        $this->line(str_repeat('─', 60));
        $this->newLine();

        $vendors = Vendor::whereNotNull('sample_work_images')->get();

        $totalImages = 0;
        foreach ($vendors as $vendor) {
            if (is_array($vendor->sample_work_images)) {
                $totalImages += count($vendor->sample_work_images);
            }
        }

        $this->line("Found {$totalImages} sample work images across {$vendors->count()} vendors");
        
        if ($totalImages === 0) {
            $this->warn('No sample work images to migrate');
            return;
        }

        $bar = $this->output->createProgressBar($totalImages);
        $bar->start();

        foreach ($vendors as $vendor) {
            if (!is_array($vendor->sample_work_images)) {
                continue;
            }

            $updatedImages = [];
            $hasChanges = false;

            foreach ($vendor->sample_work_images as $imageData) {
                try {
                    // Get the URL/path
                    $imagePath = is_array($imageData) ? ($imageData['url'] ?? null) : $imageData;

                    // Skip if already on Cloudinary
                    if (str_starts_with($imagePath, 'https://')) {
                        $updatedImages[] = $imageData;
                        $this->stats['samples']['skipped']++;
                        $bar->advance();
                        continue;
                    }

                    // Check if local file exists
                    if (!Storage::disk('public')->exists($imagePath)) {
                        $this->stats['samples']['skipped']++;
                        $bar->advance();
                        continue;
                    }

                    $fullPath = Storage::disk('public')->path($imagePath);
                    
                    $file = new \Illuminate\Http\UploadedFile(
                        $fullPath,
                        basename($imagePath),
                        Storage::disk('public')->mimeType($imagePath),
                        null,
                        true
                    );

                    // Upload to Cloudinary
                    $result = $this->cloudinaryService->uploadImage($file, 'vendor_sample_work');

                    if ($result['success'] && $result['provider'] === 'cloudinary') {
                        $updatedImages[] = [
                            'url' => $result['url'],
                            'public_id' => $result['public_id'],
                            'type' => 'cloudinary'
                        ];
                        $this->stats['samples']['success']++;
                        $hasChanges = true;
                    } else {
                        $updatedImages[] = $imageData;
                        $this->stats['samples']['skipped']++;
                    }

                } catch (\Exception $e) {
                    Log::error('Sample work migration failed: ' . $e->getMessage(), [
                        'vendor_id' => $vendor->id
                    ]);
                    $updatedImages[] = $imageData;
                    $this->stats['samples']['failed']++;
                }

                $bar->advance();
            }

            // Update vendor if there were changes
            if ($hasChanges) {
                $vendor->sample_work_images = $updatedImages;
                $vendor->save();
            }
        }

        $bar->finish();
        $this->newLine(2);
    }

    protected function migrateVideos()
    {
        $this->info('🎥 Migrating Sample Work Videos...');
        $this->line(str_repeat('─', 60));
        $this->newLine();

        $vendors = Vendor::whereNotNull('sample_work_video')->get();

        $this->line("Found {$vendors->count()} vendors with videos");
        
        if ($vendors->count() === 0) {
            $this->warn('No videos to migrate');
            return;
        }

        $bar = $this->output->createProgressBar($vendors->count());
        $bar->start();

        foreach ($vendors as $vendor) {
            try {
                $videoData = $vendor->sample_work_video;
                $videoPath = is_array($videoData) ? ($videoData['url'] ?? null) : $videoData;

                // Skip if already on Cloudinary
                if (str_starts_with($videoPath, 'https://')) {
                    $this->stats['videos']['skipped']++;
                    $bar->advance();
                    continue;
                }

                // Check if local file exists
                if (!Storage::disk('public')->exists($videoPath)) {
                    $this->stats['videos']['skipped']++;
                    $bar->advance();
                    continue;
                }

                $fullPath = Storage::disk('public')->path($videoPath);
                
                $file = new \Illuminate\Http\UploadedFile(
                    $fullPath,
                    basename($videoPath),
                    Storage::disk('public')->mimeType($videoPath),
                    null,
                    true
                );

                // Upload to Cloudinary
                $result = $this->cloudinaryService->uploadVideo($file, 'promo_videos');

                if ($result['success'] && $result['provider'] === 'cloudinary') {
                    $vendor->sample_work_video = [
                        'url' => $result['url'],
                        'public_id' => $result['public_id'],
                        'type' => 'cloudinary'
                    ];
                    $vendor->save();
                    $this->stats['videos']['success']++;
                } else {
                    $this->stats['videos']['skipped']++;
                }

            } catch (\Exception $e) {
                Log::error('Video migration failed: ' . $e->getMessage(), [
                    'vendor_id' => $vendor->id
                ]);
                $this->stats['videos']['failed']++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
    }

    protected function displaySummary()
    {
        $this->newLine();
        $this->info('📊 Migration Summary');
        $this->line(str_repeat('═', 60));
        $this->newLine();

        foreach (['profiles' => '📸 Profile Photos', 'samples' => '🖼️  Sample Work', 'videos' => '🎥 Videos'] as $key => $label) {
            $stats = $this->stats[$key];
            $total = $stats['success'] + $stats['failed'] + $stats['skipped'];
            
            if ($total > 0) {
                $this->line($label);
                $this->line("  ✅ Migrated: {$stats['success']}");
                $this->line("  ⏭️  Skipped: {$stats['skipped']}");
                $this->line("  ❌ Failed: {$stats['failed']}");
                $this->newLine();
            }
        }

        $totalSuccess = array_sum(array_column($this->stats, 'success'));
        $totalFailed = array_sum(array_column($this->stats, 'failed'));

        if ($totalSuccess > 0) {
            $this->info("✅ Successfully migrated {$totalSuccess} files to Cloudinary!");
            $this->info('🔗 View at: https://cloudinary.com/console/media_library');
        }

        if ($totalFailed > 0) {
            $this->warn("⚠️  {$totalFailed} files failed to migrate. Check logs for details.");
        }

        $this->newLine();
        $this->info('💡 Tip: You can now view all migrated files in the Admin Media Management section!');
    }
}
