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
if (!file_exists($baseDir . '.env')) {
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
    file_put_contents($baseDir . '.env', $minimalEnv);
}

// Bootstrap Laravel and handle the request...
(require_once __DIR__ . '/../bootstrap/app.php')
    ->handleRequest(Request::capture());
