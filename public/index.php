<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__ . '/../vendor/autoload.php';

// Create minimal .env if it doesn't exist (for fresh installs)
// This must happen before bootstrap so encryption service provider can load
$baseDir = __DIR__ . '/../';
$envFile = $baseDir . '.env';

// Check if .env exists and has APP_KEY
$envExists = file_exists($envFile);
$hasValidAppKey = false;

if ($envExists) {
    // Check if .env has a valid APP_KEY
    $envContent = file_get_contents($envFile);
    $hasValidAppKey = (strpos($envContent, 'APP_KEY=') !== false &&
        strpos($envContent, 'APP_KEY=base64:') !== false);
}

// Create/update .env if missing or missing APP_KEY
if (!$envExists || !$hasValidAppKey) {
    try {
        // Generate a base64-encoded random key for encryption
        $appKey = 'base64:' . base64_encode(random_bytes(32));

        // Create a minimal .env file so the app can boot
        $minimalEnv = <<<'ENV'
APP_NAME="LivestockPro ERP"
APP_ENV=production
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=http://localhost
APP_KEY={$appKey}

LOG_CHANNEL=stack
SESSION_DRIVER=file
ENV;

        $minimalEnv = str_replace('{$appKey}', $appKey, $minimalEnv);

        // Try to write the file with error checking
        $bytesWritten = @file_put_contents($envFile, $minimalEnv, LOCK_EX);

        if ($bytesWritten === false) {
            // If write fails, try to log it (for diagnostics)
            error_log('Failed to create .env file at ' . $envFile . '. Check file permissions.');
        }

        // Ensure file is readable
        if (file_exists($envFile)) {
            @chmod($envFile, 0664);
        }
    } catch (\Throwable $e) {
        error_log('Error creating .env file: ' . $e->getMessage());
    }
}

// Ensure bootstrap/cache directory exists (required by PackageManifest during bootstrap)
// This must happen before bootstrap so Laravel can cache compiled service providers
$bootstrapCacheDir = $baseDir . 'bootstrap/cache';
if (!is_dir($bootstrapCacheDir)) {
    @mkdir($bootstrapCacheDir, 0775, true);
}
@chmod($bootstrapCacheDir, 0775);

// Bootstrap Laravel and handle the request...
(require_once __DIR__ . '/../bootstrap/app.php')
    ->handleRequest(Request::capture());
