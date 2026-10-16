<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Auto-remove stale route cache on Hostinger shared hosting where artisan route:clear is not automated
if (file_exists($staleRoutes = __DIR__.'/../bootstrap/cache/routes-v7.php')) {
    @unlink($staleRoutes);
}

// Diagnostic endpoint to read latest laravel.log entries safely
if (isset($_GET['dbg']) && $_GET['dbg'] === 'setiket-check') {
    header('Content-Type: text/plain; charset=utf-8');
    echo "=== HOSTINGER DIAGNOSTICS ===\n";
    echo "PHP Version: " . phpversion() . "\n";
    echo "Time: " . date('Y-m-d H:i:s') . "\n\n";

    if (isset($_GET['flush']) && $_GET['flush'] === '1') {
        @unlink(__DIR__.'/../bootstrap/cache/routes-v7.php');
        @unlink(__DIR__.'/../bootstrap/cache/config.php');
        @unlink(__DIR__.'/../bootstrap/cache/events.php');
        $viewFiles = glob(__DIR__.'/../storage/framework/views/*');
        if ($viewFiles) {
            foreach ($viewFiles as $vf) {
                if (is_file($vf)) @unlink($vf);
            }
        }
        echo "=== FLUSHED ALL BOOTSTRAP CACHES & VIEWS ===\n\n";
    }

    $logFile = __DIR__.'/../storage/logs/laravel.log';
    if (file_exists($logFile)) {
        $lines = file($logFile);
        echo "=== LAST 120 LINES OF LARAVEL.LOG ===\n";
        echo implode('', array_slice($lines, -120));
    } else {
        echo "laravel.log not found.\n";
    }
    exit;
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
