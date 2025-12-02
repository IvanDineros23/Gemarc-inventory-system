<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DatabaseController extends Controller
{
    public function backup(Request $request)
    {
        try {
            // Run artisan db:backup command
            $exit = Artisan::call('db:backup');
            $output = Artisan::output();

            if ($exit !== 0) {
                return redirect()->back()->with('error', 'Backup failed: ' . $output);
            }

            // Get the backup directory path (storage/backups - not storage/app/backups)
            $backupDir = storage_path('backups');
            
            if (!is_dir($backupDir)) {
                return redirect()->back()->with('error', 'Backup directory not found.');
            }

            // Get all SQL files in the backup directory
            $files = glob($backupDir . '/*.sql');
            
            if (empty($files)) {
                return redirect()->back()->with('error', 'No backup file found after backup command.');
            }

            // Sort by modification time (newest first)
            usort($files, function ($a, $b) {
                return filemtime($b) <=> filemtime($a);
            });

            $latestBackup = $files[0];

            if (!is_file($latestBackup)) {
                return redirect()->back()->with('error', 'Backup file not found: ' . $latestBackup);
            }

            // Return file as download
            return response()->download($latestBackup, basename($latestBackup), [
                'Content-Type' => 'application/sql',
            ])->deleteFileAfterSend(false);
            
        } catch (\Exception $e) {
            Log::error('Backup download failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    public function restore(Request $request)
    {
        $request->validate([
            'sql_file' => ['required', 'file', 'mimes:sql,txt'],
        ]);

        try {
            $file = $request->file('sql_file');
            
            // Store uploaded file temporarily in storage/backups
            $backupDir = storage_path('backups');
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }
            
            $filename = 'restore_' . time() . '_' . $file->getClientOriginalName();
            $fullPath = $backupDir . '/' . $filename;
            
            $file->move($backupDir, $filename);

            if (!is_file($fullPath)) {
                return redirect()->back()->with('error', 'Failed to store uploaded file.');
            }

            // Run restore
            $exit = Artisan::call('db:restore', ['file' => $fullPath]);
            $output = Artisan::output();

            // Clean up uploaded file
            if (is_file($fullPath)) {
                unlink($fullPath);
            }

            if ($exit !== 0) {
                return redirect()->back()->with('error', 'Restore failed: ' . $output);
            }

            return redirect()->back()->with('success', 'Database restored successfully.');
            
        } catch (\Exception $e) {
            Log::error('Restore failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Restore failed: ' . $e->getMessage());
        }
    }
}
