<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BannerService
{
    public static function getBannersPath(): string
    {
        if (app()->runningUnitTests()) {
            return storage_path('app/banners.testing.json');
        }

        return storage_path('app/banners.json');
    }

    public static function loadBanners(): array
    {
        $path = self::getBannersPath();
        $data = file_exists($path) ? json_decode(@file_get_contents($path), true) : null;

        if (! is_array($data) || empty($data)) {
            $defaults = self::defaultBanners();
            @file_put_contents($path, json_encode($defaults, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            return $defaults;
        }

        return $data;
    }

    public static function saveBanners(array $banners): void
    {
        file_put_contents(
            self::getBannersPath(),
            json_encode(array_values($banners), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    public static function getActiveBanners(): array
    {
        $banners = self::loadBanners();
        $active = array_filter($banners, fn ($b) => !empty($b['is_active']));

        usort($active, function ($a, $b) {
            $orderA = (int) ($a['sort_order'] ?? 0);
            $orderB = (int) ($b['sort_order'] ?? 0);
            return $orderA <=> $orderB;
        });

        return array_values($active);
    }

    public static function findBanner(string $id): ?array
    {
        foreach (self::loadBanners() as $b) {
            if ((string) ($b['id'] ?? '') === (string) $id) {
                return $b;
            }
        }
        return null;
    }

    public static function storeBannerFile(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension() ?: 'webp';
        $filename = time() . '_' . Str::random(10) . '.' . $extension;

        // 1. Simpan ke Storage disk public
        Storage::disk('public')->putFileAs('banners', $file, $filename);

        // 2. Salin ke public/storage/banners dan public/images/banners jika di server nyata
        if (!app()->runningUnitTests()) {
            $destImages = public_path('images/banners');
            if (!file_exists($destImages)) {
                @mkdir($destImages, 0755, true);
            }
            $sourceFile = storage_path('app/public/banners/' . $filename);
            if (file_exists($sourceFile)) {
                @copy($sourceFile, $destImages . '/' . $filename);
            }

            $destPublicStorage = public_path('storage/banners');
            if (!file_exists($destPublicStorage)) {
                @mkdir($destPublicStorage, 0755, true);
            }
            if (file_exists($sourceFile)) {
                @copy($sourceFile, $destPublicStorage . '/' . $filename);
            }
        }

        return '/storage/banners/' . $filename;
    }

    public static function deleteBannerFile(string $path): void
    {
        if ($path === '' || str_contains($path, 'setiketbg') || str_contains($path, 'thumbnails/')) {
            return;
        }

        if (str_starts_with($path, '/storage/')) {
            $relative = substr($path, strlen('/storage/'));
            Storage::disk('public')->delete($relative);
        }

        $filename = basename($path);
        @unlink(public_path('images/banners/' . $filename));
        @unlink(public_path('storage/banners/' . $filename));
        @unlink(storage_path('app/public/banners/' . $filename));
    }

    public static function defaultBanners(): array
    {
        return [
            [
                'id' => 'setiket-promo',
                'title' => 'Platform Resmi Tiket Konser & Event SeTiket',
                'subtitle' => 'E-Ticket QR Code instan, check-in cepat, dan proteksi pembayaran 100% aman.',
                'badge_text' => 'Platform Resmi',
                'button_text' => 'Jelajahi Event',
                'target_url' => '/#events',
                'image_url' => '/images/banners/setiket-lowpoly-promo.webp',
                'has_overlay' => false,
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => '2026-10-19 03:50:00'
            ],
            [
                'id' => '1',
                'title' => 'SOUNDCHECK Vol.1 Night Party',
                'subtitle' => 'Kopi Nakal, Kemang, Jakarta Selatan • 9 Oktober 2026',
                'badge_text' => 'Featured Event',
                'button_text' => 'Beli Tiketnya di Sini',
                'target_url' => '/event/soundcheck-vol1-night-party',
                'image_url' => '/images/thumbnails/soundcheck-vol1.jpg',
                'has_overlay' => true,
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => '2026-10-01 10:00:00'
            ],
            [
                'id' => '2',
                'title' => 'YE JAKARTA 2026 WORLD TOUR',
                'subtitle' => 'Stadion Madya GBK, Jakarta Pusat • 14 November 2026',
                'badge_text' => 'Exclusive Tour',
                'button_text' => 'Beli Tiketnya di Sini',
                'target_url' => '/event/ye-jakarta-2026-world-tour',
                'image_url' => '/images/thumbnails/ye-jakarta-2026.jpg',
                'has_overlay' => true,
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => '2026-10-02 10:00:00'
            ],
            [
                'id' => '3',
                'title' => 'BIGBANG 2026-2027 WORLD TOUR <RE-BOOT>',
                'subtitle' => 'Jakarta International Stadium (JIS) • 16 Januari 2027',
                'badge_text' => 'World Tour',
                'button_text' => 'Beli Tiketnya di Sini',
                'target_url' => '/event/bigbang-2026-2027-world-tour-re-boot',
                'image_url' => '/images/thumbnails/bigbang-worldtour.jpg',
                'has_overlay' => true,
                'is_active' => true,
                'sort_order' => 4,
                'created_at' => '2026-10-03 10:00:00'
            ]
        ];
    }
}
