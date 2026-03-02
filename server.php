<?php

/**
 * Router script for PHP's built-in web server.
 *
 * Usage:
 *   php -S 127.0.0.1:8000 server.php
 *
 * This ensures existing files under /public (e.g. /build/assets/*) are served
 * directly, and everything else is routed through Laravel's front controller.
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/');

if ($uri !== '/' && file_exists(__DIR__ . '/public' . $uri)) {
    return false;
}

require_once __DIR__ . '/public/index.php';
