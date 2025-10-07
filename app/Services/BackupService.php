<?php

namespace App\Services;

use App\Models\Backup;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class BackupService
{
    /**
     * Run database backup using mysqldump
     */
    public static function runDatabaseBackup(): string
    {
        $fileName = 'backup_' . now()->format('Y_m_d_His') . '.sql';
        $backupPath = storage_path('app/backups');
        
        // Create backups directory if it doesn't exist
        if (!File::exists($backupPath)) {
            File::makeDirectory($backupPath, 0755, true);
        }
        
        $filePath = $backupPath . '/' . $fileName;

        // Build mysqldump command
        $command = sprintf(
            'mysqldump -u%s -p%s %s > %s 2>&1',
            env('DB_USERNAME'),
            env('DB_PASSWORD'),
            env('DB_DATABASE'),
            $filePath
        );

        // Execute backup
        exec($command, $output, $returnVar);

        // Check if file was created
        if (File::exists($filePath)) {
            $size = File::size($filePath);
            
            // Create backup record
            Backup::create([
                'file_name' => $fileName,
                'file_path' => $filePath,
                'type' => 'database',
                'size' => $size,
            ]);

            Log::info('Database backup created successfully', ['file' => $fileName, 'size' => $size]);
            
            return $fileName;
        }

        Log::error('Database backup failed', ['command' => $command, 'output' => $output]);
        
        return '';
    }

    /**
     * Delete old backups based on retention period
     */
    public static function deleteOldBackups(?int $days = null): int
    {
        $days = $days ?? SettingsService::get('backup_retention_days', 7);
        $cutoffDate = Carbon::now()->subDays($days);

        $oldBackups = Backup::where('created_at', '<', $cutoffDate)->get();
        $deleted = 0;

        foreach ($oldBackups as $backup) {
            if (File::exists($backup->file_path)) {
                File::delete($backup->file_path);
            }
            $backup->delete();
            $deleted++;
        }

        Log::info('Old backups cleaned up', ['deleted' => $deleted, 'retention_days' => $days]);

        return $deleted;
    }

    /**
     * Get total backup storage size
     */
    public static function getTotalBackupSize(): int
    {
        return Backup::sum('size');
    }

    /**
     * Get backup count
     */
    public static function getBackupCount(): int
    {
        return Backup::count();
    }
}

