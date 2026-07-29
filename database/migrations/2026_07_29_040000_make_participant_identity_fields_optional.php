<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Seluruh field formulir kini boleh dimatikan admin — termasuk nama, NIK,
     * WhatsApp, dan kategori yang sebelumnya terkunci. Kolomnya harus menerima
     * null, kalau tidak penyimpanan gagal begitu salah satunya dinonaktifkan.
     */
    public function up(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->string('fullname')->nullable()->change();
            $table->string('nik', 16)->nullable()->change();
            $table->string('phone')->nullable()->change();
            $table->string('category')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Isi dulu baris yang kosong supaya constraint NOT NULL bisa dipasang lagi.
        DB::table('participants')->whereNull('fullname')->update(['fullname' => '']);
        DB::table('participants')->whereNull('nik')->update(['nik' => '']);
        DB::table('participants')->whereNull('phone')->update(['phone' => '']);
        DB::table('participants')->whereNull('category')->update(['category' => '']);

        Schema::table('participants', function (Blueprint $table) {
            $table->string('fullname')->nullable(false)->change();
            $table->string('nik', 16)->nullable(false)->change();
            $table->string('phone')->nullable(false)->change();
            $table->string('category')->nullable(false)->change();
        });
    }
};
