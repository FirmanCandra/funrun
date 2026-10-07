<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Event;
use App\Models\EventCategory;
use App\Services\RestoreService;
use App\Http\Controllers\HomeController;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RestoreEventsTest extends TestCase
{
    use RefreshDatabase;

    public function test_restore_service_creates_events_and_syncs_catalog(): void
    {
        $res = RestoreService::runRestore();
        $this->assertTrue($res);

        // Verify database events
        $event1 = Event::find(1);
        $this->assertNotNull($event1);
        $this->assertStringContainsString('Masta Unimus', $event1->title);

        $event2 = Event::find(2);
        $this->assertNotNull($event2);
        $this->assertStringContainsString('EXPLORE THE MOMENT', $event2->title);

        // Verify categories for Event 1 (7 categories)
        $this->assertGreaterThanOrEqual(7, EventCategory::where('event_id', 1)->count());

        // Verify categories for Event 2
        $this->assertGreaterThanOrEqual(2, EventCategory::where('event_id', 2)->count());

        // Verify events.json catalog
        $catalog = HomeController::loadEvents();
        $masta = collect($catalog)->firstWhere('id', 1);
        $this->assertNotNull($masta);
        $this->assertEquals('ended', $masta['kategori']);
        $this->assertEquals('merchandise-inagurasi-masta-unimus', $masta['slug']);

        $explore = collect($catalog)->firstWhere('id', 2);
        $this->assertNotNull($explore);
        $this->assertEquals('ended', $explore['kategori']);
        $this->assertEquals('explore-the-moment-spekta-merbabu', $explore['slug']);
    }

    public function test_homepage_shows_ended_events(): void
    {
        RestoreService::runRestore();

        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('Merchandise Inagurasi Masta Unimus');
        $response->assertSee('EXPLORE THE MOMENT');
        $response->assertSee('Event yang Sudah Berakhir');
    }

    public function test_ended_event_detail_pages_load_with_closed_status(): void
    {
        RestoreService::runRestore();

        // Check Masta Unimus detail page
        $res1 = $this->get(route('event.show', ['identifier' => 'merchandise-inagurasi-masta-unimus']));
        $res1->assertStatus(200);
        $res1->assertSee('Penjualan Tiket Ditutup');
        $res1->assertSee('Event Selesai');

        // Check Explore The Moment detail page
        $res2 = $this->get(route('event.show', ['identifier' => 'explore-the-moment-spekta-merbabu']));
        $res2->assertStatus(200);
        $res2->assertSee('Penjualan Tiket Ditutup');
        $res2->assertSee('Event Selesai');
    }
}
