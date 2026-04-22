<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Starting manual installation process...\n";

// 1. Migrate fresh
echo "Running migrate:fresh...\n";
Artisan::call('migrate:fresh', ['--force' => true]);
echo Artisan::output();

// 2. Import Schema
echo "Importing SQL schema...\n";
$connection = config('database.default');
$schemaFile = database_path('schema/sqlite-schema.sql');

if (!file_exists($schemaFile)) {
    die("Schema file not found at $schemaFile\n");
}

$sql = file_get_contents($schemaFile);
$sqlStatements = array_filter(array_map('trim', preg_split('/;\s*\n/', $sql)));

if ($connection === 'mysql') {
    echo "Converting SQLite schema to MySQL...\n";
    $convertedStatements = [];
    foreach ($sqlStatements as $stmt) {
        if (empty($stmt) || preg_match('/^INSERT INTO.*migrations/i', $stmt)) continue;
        
        $mysqlSql = str_replace('"', '`', $stmt);
        $mysqlSql = preg_replace('/\s+check\(`[^`]+`\s+in\([^)]+\)\)\s*/i', ' ', $mysqlSql);
        $mysqlSql = preg_replace('/integer\s+primary\s+key\s+autoincrement\s+not\s+null/i', 'BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY', $mysqlSql);
        $mysqlSql = preg_replace('/integer\s+primary\s+key\s+autoincrement/i', 'BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY', $mysqlSql);
        $mysqlSql = preg_replace('/\binteger\b/i', 'BIGINT UNSIGNED', $mysqlSql);
        $mysqlSql = preg_replace('/\bBIGINT\b(?!\s+UNSIGNED)/i', 'BIGINT UNSIGNED', $mysqlSql);
        $mysqlSql = preg_replace('/\s+not\s+null/i', ' NOT NULL', $mysqlSql);
        $mysqlSql = preg_replace('/tinyint\(1\)/i', 'TINYINT(1)', $mysqlSql);
        $mysqlSql = preg_replace('/datetime/i', 'DATETIME', $mysqlSql);
        $mysqlSql = preg_replace('/\bdate\b/i', 'DATE', $mysqlSql);
        $mysqlSql = preg_replace('/\btime\b/i', 'TIME', $mysqlSql);
        $mysqlSql = preg_replace('/numeric/i', 'DECIMAL(18,2)', $mysqlSql);
        $mysqlSql = preg_replace('/\bfloat\b/i', 'DOUBLE', $mysqlSql);
        $mysqlSql = preg_replace('/\btext\b/i', 'LONGTEXT', $mysqlSql);
        $mysqlSql = preg_replace('/varchar(?!\s*\()/i', 'VARCHAR(255)', $mysqlSql);
        
        $convertedStatements[] = $mysqlSql;
    }

    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    foreach ($convertedStatements as $stmt) {
        try {
            DB::unprepared($stmt . ';');
        } catch (\Throwable $e) {
            if (str_contains($e->getMessage(), 'already exists') || str_contains($e->getMessage(), '1061')) continue;
            echo "Error executing: $stmt\nError: " . $e->getMessage() . "\n";
        }
    }
    DB::statement('SET FOREIGN_KEY_CHECKS=1');
} else {
    foreach ($sqlStatements as $stmt) {
        if (empty($stmt) || preg_match('/^INSERT INTO.*migrations/i', $stmt)) continue;
        try {
            DB::unprepared($stmt . ';');
        } catch (\Throwable $e) {
            if (str_contains($e->getMessage(), 'already exists')) continue;
            echo "Error: " . $e->getMessage() . "\n";
        }
    }
}
echo "Schema imported.\n";

// 3. Seed
echo "Seeding database...\n";
Artisan::call('db:seed', ['--force' => true]);
echo Artisan::output();

// 4. Final touches
echo "Generating key...\n";
Artisan::call('key:generate', ['--force' => true]);
echo Artisan::output();

echo "Linking storage...\n";
Artisan::call('storage:link', ['--force' => true]);
echo Artisan::output();

echo "Creating installed lock file...\n";
file_put_contents(storage_path('installed'), date('Y-m-d H:i:s'));

echo "Installation complete!\n";
