<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Backup;
use App\Services\BackupService;
use Illuminate\Http\Request;

class BackupController extends Controller
{
    /**
     * Display backup files
     */
    public function index()
    {
        $backups = Backup::latest('created_at')->paginate(10);
        
        $stats = [
            'total_backups' => Backup::count(),
            'total_size' => BackupService::getTotalBackupSize(),
            'latest_backup' => Backup::latest('created_at')->first(),
        ];

        return view('superadmin.settings.backups', compact('backups', 'stats'));
    }

    /**
     * Create a new database backup
     */
    public function create()
    {
        try {
            $fileName = BackupService::runDatabaseBackup();
            
            if ($fileName) {
                // Cleanup old backups
                BackupService::deleteOldBackups();
                
                return back()->with('success', 'Database backup created successfully! 🇬🇭 File: ' . $fileName);
            }

            return back()->with('error', 'Backup creation failed. Check logs for details.');
        } catch (\Exception $e) {
            return back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    /**
     * Download a backup file
     */
    public function download($id)
    {
        $backup = Backup::findOrFail($id);

        if (file_exists($backup->file_path)) {
            return response()->download($backup->file_path);
        }

        return back()->with('error', 'Backup file not found on disk.');
    }

    /**
     * Delete a backup
     */
    public function destroy($id)
    {
        $backup = Backup::findOrFail($id);

        if (file_exists($backup->file_path)) {
            unlink($backup->file_path);
        }

        $backup->delete();

        return back()->with('success', 'Backup deleted successfully.');
    }
}
