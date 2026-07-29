<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Definisi formulir pendaftaran per event.
     *
     * Tiap event punya daftar field sendiri, diatur oleh admin yang menangani
     * event tersebut. Ada dua macam baris:
     *
     * - is_core = true  : field bawaan (tanggal lahir, jersey, dll). Adminnya
     *                     hanya boleh mengubah label/wajib/aktif, bukan tipenya,
     *                     karena kolomnya sudah ada di tabel `participants`.
     * - is_core = false : field tambahan bebas. Jawabannya masuk ke kolom JSON
     *                     `participants.custom_data`.
     *
     * Field inti yang dipakai sistem (nama, NIK, WhatsApp, kategori) sengaja
     * tidak disimpan di sini — semuanya wajib dan tidak boleh dimatikan.
     */
    public function up(): void
    {
        Schema::create('event_form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('key');
            $table->string('label');
            // text | textarea | number | date | consent | select (khusus core)
            $table->string('type')->default('text');
            $table->boolean('is_core')->default(false);
            $table->boolean('enabled')->default(true);
            $table->boolean('required')->default(false);
            $table->string('placeholder')->nullable();
            $table->string('help_text')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            // Key harus unik dalam satu event supaya jawaban tidak saling timpa.
            $table->unique(['event_id', 'key']);
        });

        Schema::table('participants', function (Blueprint $table) {
            $table->json('custom_data')->nullable()->after('category');

            // `city` semula NOT NULL. Begitu admin boleh menjadikannya opsional
            // atau mematikannya, kolomnya harus menerima null seperti kolom
            // bawaan lain yang bisa diatur (dob, gender, address, jersey_size,
            // emergency_contact, medical_history — semuanya sudah nullable).
            $table->string('city', 255)->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropColumn('custom_data');
        });

        DB::table('participants')->whereNull('city')->update(['city' => '']);

        Schema::table('participants', function (Blueprint $table) {
            $table->string('city', 255)->nullable(false)->default('')->change();
        });

        Schema::dropIfExists('event_form_fields');
    }
};
