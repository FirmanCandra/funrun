<?php
header('Content-Type: text/plain; charset=utf-8');

echo "=== SETIKET PRODUCTION DIAGNOSTIC ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n";
echo "PHP Version: " . phpversion() . "\n";
echo "Current File: " . __FILE__ . "\n";
echo "DocRoot: " . ($_SERVER['DOCUMENT_ROOT'] ?? '-') . "\n";

$head = @file_get_contents(__DIR__ . '/../.git/HEAD');
echo "Git HEAD: " . trim($head ?: 'unknown') . "\n";
if ($head && str_starts_with(trim($head), 'ref: ')) {
    $refPath = __DIR__ . '/../.git/' . trim(substr(trim($head), 5));
    echo "Git Ref Hash: " . trim(@file_get_contents($refPath) ?: 'not found') . "\n";
}

$loginView = @file_get_contents(__DIR__ . '/../resources/views/auth/login.blade.php');
echo "login.blade.php size: " . strlen($loginView) . " bytes\n";
echo "login.blade.php has auth.google: " . (str_contains($loginView, 'auth.google') ? "YES" : "NO") . "\n\n";

// 1. Flush any bootstrap caches
$routeCache = __DIR__ . '/../bootstrap/cache/routes-v7.php';
$configCache = __DIR__ . '/../bootstrap/cache/config.php';
$eventsCache = __DIR__ . '/../bootstrap/cache/events.php';

echo "--- CACHE STATUS ---\n";
if (file_exists($routeCache)) {
    echo "Found route cache (modified: " . date('Y-m-d H:i:s', filemtime($routeCache)) . "). Deleting... ";
    @unlink($routeCache);
    echo file_exists($routeCache) ? "FAILED\n" : "DELETED OK\n";
} else {
    echo "No route cache found.\n";
}

if (file_exists($configCache)) {
    echo "Found config cache. Deleting... ";
    @unlink($configCache);
    echo file_exists($configCache) ? "FAILED\n" : "DELETED OK\n";
} else {
    echo "No config cache found.\n";
}

$views = glob(__DIR__ . '/../storage/framework/views/*');
$deletedViews = 0;
if ($views) {
    foreach ($views as $v) {
        if (is_file($v)) {
            @unlink($v);
            $deletedViews++;
        }
    }
}
echo "Cleared compiled views: $deletedViews files\n\n";

// 2. Clear log if requested
$log = __DIR__ . '/../storage/logs/laravel.log';
if (isset($_GET['clear_log']) && $_GET['clear_log'] === '1') {
    @file_put_contents($log, '');
    echo "=== LOG CLEARED AT " . date('Y-m-d H:i:s') . " ===\n\n";
}

// 2b. Sinkronisasi banner resmi setiket-promo jika belum ada di storage/app/banners.json
$bannersPath = __DIR__ . '/../storage/app/banners.json';
if (file_exists($bannersPath)) {
    $existingBanners = json_decode(@file_get_contents($bannersPath), true) ?: [];
    $hasPromo = false;
    foreach ($existingBanners as $b) {
        if (($b['id'] ?? '') === 'setiket-promo') {
            $hasPromo = true;
            break;
        }
    }
    if (!$hasPromo) {
        array_unshift($existingBanners, [
            'id' => 'setiket-promo',
            'title' => 'Platform Resmi Tiket Konser & Event SeTiket',
            'subtitle' => 'E-Ticket QR Code instan, check-in cepat, dan proteksi pembayaran 100% aman.',
            'badge_text' => 'Platform Resmi',
            'button_text' => 'Jelajahi Event',
            'target_url' => '/#events',
            'image_url' => '/images/banners/setiket-hero-promo.webp',
            'has_overlay' => false,
            'is_active' => true,
            'sort_order' => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        @file_put_contents($bannersPath, json_encode(array_values($existingBanners), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        echo "Sync: Added setiket-promo banner to storage/app/banners.json\n";
    }
}


// 3. Read latest Laravel error log
echo "--- LARAVEL.LOG STATUS ---\n";
if (file_exists($log)) {
    $lines = file($log);
    $total = count($lines);
    echo "Total lines: $total\n";
    echo "--- LAST 300 LINES OF LARAVEL.LOG ---\n";
    echo implode('', array_slice($lines, -300));
} else {
    echo "laravel.log does not exist.\n";
}
