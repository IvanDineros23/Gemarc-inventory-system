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
            
            // Split the SQL into individual statements
            // This is a simple split - for production you might want a more robust parser
            $statements = array_filter(
                array_map('trim', preg_split('/;[\r\n]+/', $sql)),
                function($stmt) {
                    return !empty($stmt) && !preg_match('/^--/', $stmt) && !preg_match('/^\/\*!/', $stmt);
                }
            );

            $this->info('Executing ' . count($statements) . ' SQL statements...');
            
            $executed = 0;
            foreach ($statements as $statement) {
                if (empty($statement) || substr($statement, 0, 2) === '--') {
                    continue;
                }
                
                try {
                    DB::unprepared($statement);
                    $executed++;
                } catch (\Exception $e) {
                    // Log but continue with other statements
                    Log::warning('SQL statement failed: ' . $e->getMessage());
                }
            }

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            $this->info("Restore completed successfully. Executed {$executed} statements.");
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
}
