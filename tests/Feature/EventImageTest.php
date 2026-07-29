<?php

namespace Tests\Feature;

use App\Http\Controllers\HomeController;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Gambar event adalah materi promosi yang dipegang penyelenggara, jadi admin
 * event boleh mengurusnya sendiri — beda dengan data event lain yang dikunci
 * super admin. Batasnya: hanya event yang dia tangani.
 */
class EventImageTest extends TestCase
{
    use RefreshDatabase;

    private Event $voltRhythm;

    private Event $stepUpFest;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        // events.json adalah sumber data halaman publik; isinya dikunci di sini
        // supaya pengujian tidak tergantung daftar event bawaan.
        HomeController::saveEvents([
            $this->eventJson(1, 'VOLT RHYTHM 2026'),
            $this->eventJson(2, 'STEP UP FEST 2026'),
        ]);

        $this->voltRhythm = $this->makeEvent(1, 'VOLT RHYTHM 2026');
        $this->stepUpFest = $this->makeEvent(2, 'STEP UP FEST 2026');
    }

    protected function tearDown(): void
    {
        @unlink(HomeController::getEventsPath());

        parent::tearDown();
    }

    private function eventJson(int $id, string $nama, string $thumbnail = ''): array
    {
        return [
            'id' => $id,
            'nama' => $nama,
            'lokasi' => 'Jakarta',
            'tanggal' => '15 September 2026',
            'harga' => 150000,
            'thumbnail' => $thumbnail,
            'kategori' => 'upcoming',
            'urlBeli' => 'https://wa.me/6289681201941',
        ];
    }

    private function makeEvent(int $id, string $title): Event
    {
        return Event::create([
            'id' => $id,
            'title' => $title,
            'date' => '2026-09-15',
            'location' => 'Jakarta',
            'quota' => 5000,
        ]);
    }

    /**
     * Thumbnail event tertentu seperti yang tersimpan di events.json.
     */
    private function thumbnailOf(int $eventId): string
    {
        foreach (HomeController::loadEvents() as $ev) {
            if ($ev['id'] === $eventId) {
                return $ev['thumbnail'];
            }
        }

        return '';
    }

    // ===== Akses halaman =====

    public function test_event_admin_can_open_the_image_page_for_their_own_event(): void
    {
        $admin = User::factory()->admin($this->voltRhythm->id)->create();

        $this->actingAs($admin)->get('/admin/event-image')
            ->assertOk()
            ->assertSee('VOLT RHYTHM 2026')
            ->assertDontSee('STEP UP FEST 2026');
    }

    public function test_participant_cannot_open_the_image_page(): void
    {
        $peserta = User::factory()->create();

        $this->actingAs($peserta)->get('/admin/event-image')->assertForbidden();
    }

    public function test_super_admin_can_choose_any_event(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)->get('/admin/event-image?event_id='.$this->stepUpFest->id)
            ->assertOk()
            ->assertSee('STEP UP FEST 2026');
    }

    // ===== Unggah =====

    public function test_event_admin_can_upload_an_image_for_their_event(): void
    {
        $admin = User::factory()->admin($this->voltRhythm->id)->create();

        $this->actingAs($admin)->post('/admin/event-image', [
            'thumbnail' => UploadedFile::fake()->image('poster.jpg', 1280, 720),
        ])->assertRedirect();

        $tersimpan = $this->thumbnailOf($this->voltRhythm->id);

        $this->assertStringStartsWith('/storage/thumbnails/', $tersimpan);
        Storage::disk('public')->assertExists(substr($tersimpan, strlen('/storage/')));
    }

    public function test_uploaded_image_appears_on_the_public_event_page(): void
    {
        $admin = User::factory()->admin($this->voltRhythm->id)->create();

        $this->actingAs($admin)->post('/admin/event-image', [
            'thumbnail' => UploadedFile::fake()->image('poster.jpg'),
        ]);

        $this->get('/')->assertOk()->assertSee($this->thumbnailOf($this->voltRhythm->id));
    }

    public function test_event_admin_cannot_change_the_image_of_another_event(): void
    {
        $admin = User::factory()->admin($this->voltRhythm->id)->create();

        // event_id milik event lain dipaksakan lewat form.
        $this->actingAs($admin)->post('/admin/event-image', [
            'event_id' => $this->stepUpFest->id,
            'thumbnail' => UploadedFile::fake()->image('poster.jpg'),
        ])->assertRedirect();

        $this->assertNotSame('', $this->thumbnailOf($this->voltRhythm->id));
        $this->assertSame('', $this->thumbnailOf($this->stepUpFest->id));
    }

    public function test_oversized_and_non_image_files_are_rejected(): void
    {
        $admin = User::factory()->admin($this->voltRhythm->id)->create();

        $this->actingAs($admin)->post('/admin/event-image', [
            'thumbnail' => UploadedFile::fake()->create('daftar.pdf', 100, 'application/pdf'),
        ])->assertSessionHasErrors('thumbnail');

        $this->actingAs($admin)->post('/admin/event-image', [
            'thumbnail' => UploadedFile::fake()->image('poster.jpg')->size(2048),
        ])->assertSessionHasErrors('thumbnail');

        $this->assertSame('', $this->thumbnailOf($this->voltRhythm->id));
    }

    // ===== Ganti & hapus =====

    public function test_replacing_an_image_removes_the_file_it_replaces(): void
    {
        $admin = User::factory()->admin($this->voltRhythm->id)->create();

        $this->actingAs($admin)->post('/admin/event-image', [
            'thumbnail' => UploadedFile::fake()->image('lama.jpg'),
        ]);
        $lama = $this->thumbnailOf($this->voltRhythm->id);

        $this->actingAs($admin)->post('/admin/event-image', [
            'thumbnail' => UploadedFile::fake()->image('baru.jpg'),
        ]);
        $baru = $this->thumbnailOf($this->voltRhythm->id);

        $this->assertNotSame($lama, $baru);
        Storage::disk('public')->assertMissing(substr($lama, strlen('/storage/')));
        Storage::disk('public')->assertExists(substr($baru, strlen('/storage/')));
    }

    public function test_event_admin_can_remove_the_image(): void
    {
        $admin = User::factory()->admin($this->voltRhythm->id)->create();

        $this->actingAs($admin)->post('/admin/event-image', [
            'thumbnail' => UploadedFile::fake()->image('poster.jpg'),
        ]);
        $path = $this->thumbnailOf($this->voltRhythm->id);

        $this->actingAs($admin)->delete('/admin/event-image')->assertRedirect();

        $this->assertSame('', $this->thumbnailOf($this->voltRhythm->id));
        Storage::disk('public')->assertMissing(substr($path, strlen('/storage/')));
    }

    public function test_a_file_still_used_by_another_event_survives_removal(): void
    {
        // Dua event menunjuk berkas yang sama — menghapus salah satunya tidak
        // boleh mengosongkan gambar event yang lain.
        $bersama = '/storage/thumbnails/bersama.jpg';
        Storage::disk('public')->put('thumbnails/bersama.jpg', 'isi');

        HomeController::saveEvents([
            $this->eventJson(1, 'VOLT RHYTHM 2026', $bersama),
            $this->eventJson(2, 'STEP UP FEST 2026', $bersama),
        ]);

        $admin = User::factory()->admin($this->voltRhythm->id)->create();
        $this->actingAs($admin)->delete('/admin/event-image')->assertRedirect();

        $this->assertSame('', $this->thumbnailOf($this->voltRhythm->id));
        $this->assertSame($bersama, $this->thumbnailOf($this->stepUpFest->id));
        Storage::disk('public')->assertExists('thumbnails/bersama.jpg');
    }
}
