<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DbRestore extends Command
{
    protected $signature = 'db:restore {file : path to SQL file to restore}';

    protected $description = 'Restore a MySQL database from a SQL dump using native PHP';

    public function handle(): int
    {
        $file = $this->argument('file');
        
        if (! is_file($file) || ! is_readable($file)) {
            $this->error('SQL file not found or not readable: ' . $file);
            return 1;
        }

        $this->info("Restoring SQL file: {$file}");

        try {
            // Read the SQL file
            $sql = file_get_contents($file);
            
            if (empty($sql)) {
                $this->error('SQL file is empty.');
                return 1;
            }

            // Disable foreign key checks during restore
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            DB::statement('SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO"');
            DB::statement('SET time_zone = "+00:00"');
            
            // Parse SQL statements properly
            $statements = $this->parseSqlFile($sql);

            $this->info('Executing ' . count($statements) . ' SQL statements...');
            
            $executed = 0;
            $errors = 0;
            
            foreach ($statements as $statement) {
                if (empty($statement)) {
                    continue;
                }
                
                try {
                    DB::unprepared($statement);
                    $executed++;
                    
                    // Show progress for every 10 statements
                    if ($executed % 10 === 0) {
                        $this->info("Executed {$executed} statements...");
                    }
                } catch (\Exception $e) {
                    $errors++;
                    Log::warning('SQL statement failed: ' . substr($statement, 0, 100) . '... Error: ' . $e->getMessage());
                }
            }

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            $this->info("Restore completed. Executed {$executed} statements successfully, {$errors} failed.");
            return 0;
            
        } catch (\Exception $e) {
            // Re-enable foreign key checks even on error
            try {
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
            } catch (\Exception $ignored) {}
            
            Log::error('DbRestore failed: ' . $e->getMessage());
            $this->error('Restore failed: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Parse SQL file into individual statements
     * Handles multi-line statements, comments, and MySQL-specific syntax
     */
    protected function parseSqlFile(string $sql): array
    {
        $statements = [];
        $currentStatement = '';
        $lines = explode("\n", $sql);
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            // Skip empty lines
            if (empty($line)) {
                continue;
            }
            
            // Skip single-line comments
            if (substr($line, 0, 2) === '--' || substr($line, 0, 1) === '#') {
                continue;
            }
            
            // Handle MySQL-specific comments like /*!40101 ... */
            if (preg_match('/^\/\*!(\d+)?\s*(.*?)\s*\*\/;?$/', $line, $matches)) {
                // Execute the content inside these special comments
                if (!empty($matches[2])) {
                    $statements[] = trim($matches[2]);
                }
                continue;
            }
            
            // Skip regular multi-line comments
            if (substr($line, 0, 2) === '/*' && strpos($line, '*/') === false) {
                continue;
            }
            
            // Add line to current statement
            $currentStatement .= $line . ' ';
            
            // Check if statement is complete (ends with semicolon)
            if (substr(rtrim($line), -1) === ';') {
                $currentStatement = trim($currentStatement);
                
                if (!empty($currentStatement)) {
                    $statements[] = $currentStatement;
                }
                
                $currentStatement = '';
            }
        }
        
        // Add any remaining statement
        if (!empty(trim($currentStatement))) {
            $statements[] = trim($currentStatement);
        }
        
        return array_filter($statements);
    }
}
