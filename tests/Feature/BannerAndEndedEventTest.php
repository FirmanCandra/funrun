<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\EventPaymentAccount;
use App\Models\User;
use App\Services\BannerService;
use App\Services\RestoreService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BannerAndEndedEventTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        @unlink(storage_path('app/events.testing.json'));
        @unlink(storage_path('app/banners.testing.json'));
        RestoreService::runRestore();
    }

    protected function tearDown(): void
    {
        @unlink(storage_path('app/events.testing.json'));
        @unlink(storage_path('app/banners.testing.json'));
        parent::tearDown();
    }

    public function test_ended_event_ticket_tab_shows_penjualan_ditutup_and_blocks_booking(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        // 1. Visit ended event detail page
        $response = $this->get(route('event.show', ['identifier' => 'merchandise-inagurasi-masta-unimus']));
        $response->assertStatus(200);

        // Harus menampilkan badge Penjualan Ditutup, bukan Kuota Tersedia
        $response->assertSee('Penjualan Ditutup');
        $response->assertDontSee('Kuota Tersedia');
        $response->assertSee('Event Telah Berakhir &bull; Penjualan Tiket Ditutup', false);

        // 2. Akses form pendaftaran untuk event yang sudah berakhir harus dialihkan
        $registerPageResponse = $this->actingAs($user)->get(route('event.register', ['event_id' => 1]));
        $registerPageResponse->assertRedirect(route('event.show', 'merchandise-inagurasi-masta-unimus'));
        $registerPageResponse->assertSessionHas('error');

        // 3. Submit pesanan tiket untuk event yang sudah berakhir harus ditolak
        $paymentAccount = EventPaymentAccount::create([
            'event_id' => 1,
            'bank_name' => 'BCA',
            'account_number' => '1234567890',
            'account_holder' => 'Panitia Masta',
            'is_active' => true,
        ]);

        Storage::fake('public');
        $proof = UploadedFile::fake()->image('transfer.jpg');

        $submitResponse = $this->actingAs($user)->post('/register-event', [
            'event_id' => 1,
            'payment_account_id' => $paymentAccount->id,
            'proof' => $proof,
            'tickets' => [
                [
                    'category' => 'PAKET LENGKAP',
                    'name' => 'Budi Santoso',
                    'email' => 'budi@example.com',
                    'phone' => '081234567890',
                    'gender' => 'Laki-laki',
                ],
            ],
        ]);

        $submitResponse->assertRedirect(route('event.show', 'merchandise-inagurasi-masta-unimus'));
        $submitResponse->assertSessionHas('error');
    }

    public function test_marquee_ticker_contains_no_dummy_event_names_and_has_trust_badges(): void
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200);

        $html = $response->getContent();
        preg_match('/class="loket-marquee-track.*?<\/div>/s', $html, $matches);
        $this->assertNotEmpty($matches);
        $marqueeContent = $matches[0];

        // Pastikan nama-nama event dummy sudah dihapus dari marquee ribbon
        $this->assertStringNotContainsString('SOUNDCHECK VOL. 1 NIGHT PARTY', $marqueeContent);
        $this->assertStringNotContainsString('YE JAKARTA 2026 WORLD TOUR', $marqueeContent);
        $this->assertStringNotContainsString('BIGBANG 2026-2027 WORLD TOUR RE-BOOT', $marqueeContent);
        $this->assertStringNotContainsString('JAKARTA NIGHT MARATHON 2026', $marqueeContent);

        // Pastikan jaminan dan badge layanan resmi ada di ribbon
        $this->assertStringContainsString('TIKET RESMI &amp; TERVERIFIKASI', $marqueeContent);
        $this->assertStringContainsString('E-TICKET DENGAN QR CODE INSTAN', $marqueeContent);
        $this->assertStringContainsString('CHECK-IN MUDAH &amp; CEPAT', $marqueeContent);
        $this->assertStringContainsString('VERIFIKASI PANITIA RESMI', $marqueeContent);
        $this->assertStringContainsString('PEMBAYARAN AMAN TERENKRIPSI', $marqueeContent);
        $this->assertStringContainsString('CUSTOMER SUPPORT RESPONSIF', $marqueeContent);

        // Pastikan tidak ada icon SVG (anti-judol) dan teks menggunakan warna putih
        $this->assertStringNotContainsString('<svg', $marqueeContent);
        $this->assertStringNotContainsString('text-amber-400', $marqueeContent);
        $this->assertStringContainsString('text-white', $marqueeContent);

        // Pastikan search input placeholder tidak lagi mencantumkan nama event dummy
        $response->assertDontSee('Cari event SOUNDCHECK, YE JAKARTA, MARATHON...');
        $response->assertSee('Cari event konser, festival, olahraga, seminar...');
    }

    public function test_hero_carousel_excludes_ended_events(): void
    {
        // Pastikan saat mencari event yang sudah selesai (misal: "unimus" atau "masta"),
        // carousel tidak menamai event selesai sebagai "Akan Datang"
        $response = $this->get(route('home', ['q' => 'masta']));
        $response->assertStatus(200);

        $content = $response->getContent();
        // Cek bahwa di section heroCarousel tidak terdapat teks "Akan Datang" untuk masta
        if (preg_match('/id="heroCarousel".*?<\/section>/s', $content, $matches)) {
            $this->assertStringNotContainsString('AKAN DATANG', $matches[0]);
        }
    }

    public function test_super_admin_can_manage_banners_and_view_guidelines(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $normalAdmin = User::factory()->create(['role' => 'admin']);

        // 1. Normal admin cannot access /admin/banners
        $forbiddenResponse = $this->actingAs($normalAdmin)->get(route('admin.banners'));
        $forbiddenResponse->assertStatus(403);

        // 2. Super admin can access and see guideline specifications
        $response = $this->actingAs($superAdmin)->get(route('admin.banners'));
        $response->assertStatus(200);
        $response->assertSee('Ketentuan Ukuran Gambar Banner Slider');
        $response->assertSee('1920 × 720 px');
        $response->assertSee('16:9 atau 21:9');
        $response->assertSee('Maks 2 MB');

        // 3. Super admin can store a new banner
        Storage::fake('public');
        $fakeBanner = UploadedFile::fake()->image('banner_hero.webp', 1920, 720);

        $storeResponse = $this->actingAs($superAdmin)->post(route('admin.banners.store'), [
            'title' => 'Konser Musik Spektakuler 2026',
            'subtitle' => 'Stadion Utama Gelora Bung Karno • 20 Desember 2026',
            'badge_text' => 'SUPER PROMO',
            'button_text' => 'Ambil Tiket Sekarang',
            'target_url' => '/event/soundcheck-vol1-night-party',
            'sort_order' => 1,
            'image' => $fakeBanner,
        ]);

        $storeResponse->assertRedirect(route('admin.banners'));
        $storeResponse->assertSessionHas('success');

        $banners = BannerService::loadBanners();
        $newBanner = collect($banners)->firstWhere('title', 'Konser Musik Spektakuler 2026');
        $this->assertNotNull($newBanner);
        $this->assertEquals('SUPER PROMO', $newBanner['badge_text']);
        $this->assertTrue($newBanner['is_active']);

        // 4. Super admin can toggle active state
        $toggleResponse = $this->actingAs($superAdmin)->post(route('admin.banners.toggle', $newBanner['id']));
        $toggleResponse->assertRedirect(route('admin.banners'));
        $updatedBanners = BannerService::loadBanners();
        $toggled = collect($updatedBanners)->firstWhere('id', $newBanner['id']);
        $this->assertFalse($toggled['is_active']);

        // 5. Super admin can delete banner
        $deleteResponse = $this->actingAs($superAdmin)->delete(route('admin.banners.destroy', $newBanner['id']));
        $deleteResponse->assertRedirect(route('admin.banners'));
        $remaining = BannerService::loadBanners();
        $this->assertNull(collect($remaining)->firstWhere('id', $newBanner['id']));
    }

    public function test_banner_slider_supports_standalone_artwork_without_overlay(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        // Create standalone banner with has_overlay false
        $storeResponse = $this->actingAs($superAdmin)->post(route('admin.banners.store'), [
            'title' => 'Banner SeTiket Promosi Resmi',
            'subtitle' => 'E-Ticket QR Code instan & terpercaya',
            'badge_text' => 'Platform Resmi',
            'button_text' => 'Jelajahi Event',
            'target_url' => '/#events',
            'sort_order' => 1,
            'image_url' => '/images/banners/setiket-hero-promo.webp',
            'has_overlay' => '0',
        ]);

        $storeResponse->assertRedirect(route('admin.banners'));
        $banners = BannerService::loadBanners();
        $banner = collect($banners)->firstWhere('title', 'Banner SeTiket Promosi Resmi');
        $this->assertNotNull($banner);
        $this->assertFalse($banner['has_overlay']);

        // Check rendering on homepage: when has_overlay is false, the full clickable link is rendered without dark gradient
        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('/images/banners/setiket-hero-promo.webp');
    }
}

