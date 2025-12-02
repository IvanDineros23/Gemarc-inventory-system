<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DbBackup extends Command
{
    protected $signature = 'db:backup {--path= : custom path to save backup file}';

    protected $description = 'Create a backup of the configured database using native PHP';

    public function handle(): int
    {
        $this->info('Starting database backup...');

        $database = config('database.connections.mysql.database');
        
        if (empty($database)) {
            $this->error('Database name is empty in configuration.');
            return 1;
        }

        $backupDir = storage_path('backups');
        if (! is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $timestamp = date('Ymd_His');
        $defaultFile = "{$backupDir}/backup_{$database}_{$timestamp}.sql";
        $file = $this->option('path') ? $this->option('path') : $defaultFile;

        try {
            $this->info('Exporting database structure and data...');
            
            $sql = $this->generateDump($database);
            
            file_put_contents($file, $sql);
            
            $this->info(sprintf('Backup completed: %s (%.2f KB)', $file, filesize($file) / 1024));
            return 0;
            
        } catch (\Exception $e) {
            Log::error('DbBackup failed: ' . $e->getMessage());
            $this->error('Backup failed: ' . $e->getMessage());
            return 1;
        }
    }

    protected function generateDump(string $database): string
    {
        $dump = "-- MySQL/MariaDB Database Backup\n";
        $dump .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        $dump .= "-- Database: {$database}\n\n";
        $dump .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
        $dump .= "SET time_zone = \"+00:00\";\n\n";
        $dump .= "/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\n";
        $dump .= "/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;\n";
        $dump .= "/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;\n";
        $dump .= "/*!40101 SET NAMES utf8mb4 */;\n\n";

        // Get all tables
        $tables = DB::select('SHOW TABLES');
        $tableKey = "Tables_in_{$database}";

        foreach ($tables as $table) {
            $tableName = $table->$tableKey;
            $this->info("Backing up table: {$tableName}");
            
            $dump .= "\n--\n-- Table structure for table `{$tableName}`\n--\n\n";
            $dump .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            
            // Get CREATE TABLE statement
            $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $dump .= $createTable[0]->{'Create Table'} . ";\n\n";
            
            // Get table data
            $rows = DB::table($tableName)->get();
            
            if ($rows->count() > 0) {
                $dump .= "--\n-- Dumping data for table `{$tableName}`\n--\n\n";
                
                foreach ($rows as $row) {
                    $values = [];
                    foreach ((array) $row as $value) {
                        if (is_null($value)) {
                            $values[] = 'NULL';
                        } else {
                            $values[] = "'" . addslashes($value) . "'";
                        }
                    }
                    
                    $columns = array_keys((array) $row);
                    $columnsList = '`' . implode('`, `', $columns) . '`';
                    $valuesList = implode(', ', $values);
                    
                    $dump .= "INSERT INTO `{$tableName}` ({$columnsList}) VALUES ({$valuesList});\n";
                }
                
                $dump .= "\n";
            }
        }

        $dump .= "/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;\n";
        $dump .= "/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;\n";
        $dump .= "/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;\n";

        return $dump;
    }
}
