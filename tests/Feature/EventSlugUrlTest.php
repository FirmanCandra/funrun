<?php

namespace Tests\Feature;

use App\Http\Controllers\HomeController;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventSlugUrlTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Siapkan events.testing.json dengan slug yang jelas
        HomeController::saveEvents([
            [
                'id' => 1,
                'slug' => 'soundcheck-vol1-night-party',
                'nama' => 'SOUNDCHECK Vol.1 Night Party',
                'lokasi' => 'Kemang, Jakarta',
                'tanggal' => '9 Oktober 2026',
                'harga' => 35000,
                'thumbnail' => '/images/thumbnails/soundcheck-vol1.jpg',
                'kategori' => 'highlight',
                'urlBeli' => 'https://wa.me/6289681201941',
            ],
            [
                'id' => 2,
                'slug' => 'ye-jakarta-2026-world-tour',
                'nama' => 'YE JAKARTA 2026 WORLD TOUR',
                'lokasi' => 'Stadion Madya GBK',
                'tanggal' => '14 November 2026',
                'harga' => 850000,
                'thumbnail' => '/images/thumbnails/ye-jakarta-2026.jpg',
                'kategori' => 'upcoming',
                'urlBeli' => 'https://wa.me/6289681201941',
            ],
        ]);

        Event::create([
            'id' => 1,
            'title' => 'SOUNDCHECK Vol.1 Night Party',
            'date' => '2026-10-09',
            'location' => 'Kemang, Jakarta',
            'quota' => 5000,
        ]);

        Event::create([
            'id' => 2,
            'title' => 'YE JAKARTA 2026 WORLD TOUR',
            'date' => '2026-11-14',
            'location' => 'Stadion Madya GBK',
            'quota' => 5000,
        ]);
    }

    protected function tearDown(): void
    {
        @unlink(HomeController::getEventsPath());
        parent::tearDown();
    }

    public function test_event_page_can_be_accessed_via_slug(): void
    {
        $response = $this->get('/event/soundcheck-vol1-night-party');

        $response->assertOk()
            ->assertSee('SOUNDCHECK Vol.1 Night Party')
            ->assertSee('Kemang, Jakarta');
    }

    public function test_numeric_id_redirects_301_to_canonical_slug(): void
    {
        $response = $this->get('/event/1');

        $response->assertStatus(301);
        $response->assertRedirect('/event/soundcheck-vol1-night-party');

        // Mengikuti redirect menuju halaman 200 OK
        $followResponse = $this->followRedirects($response);
        $followResponse->assertOk()
            ->assertSee('SOUNDCHECK Vol.1 Night Party');
    }

    public function test_slug_variant_redirects_301_to_canonical_slug(): void
    {
        // Variasi penulisan dengan tanda hubung ekstra vol-1
        $response = $this->get('/event/soundcheck-vol-1-night-party');

        $response->assertStatus(301);
        $response->assertRedirect('/event/soundcheck-vol1-night-party');
    }

    public function test_non_existent_event_slug_returns_404(): void
    {
        $response = $this->get('/event/event-tidak-ada-2026');

        $response->assertNotFound();
    }

    public function test_landing_page_renders_slug_links_instead_of_numeric_ids(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('/event/soundcheck-vol1-night-party');
        $response->assertSee('/event/ye-jakarta-2026-world-tour');
        $response->assertDontSee('/event/1"');
        $response->assertDontSee('/event/2"');
    }

    public function test_share_text_and_canonical_url_use_slug(): void
    {
        $response = $this->get('/event/soundcheck-vol1-night-party');

        $response->assertOk();
        $response->assertSee(urlencode('/event/soundcheck-vol1-night-party'));
        $response->assertSee('soundcheck-vol1-night-party');
    }
}
