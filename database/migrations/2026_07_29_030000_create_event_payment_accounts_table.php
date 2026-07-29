<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Rekening tujuan transfer, satu daftar per event.
     *
     * Sebelumnya rekening menumpang di kolom JSON `events.payment_methods` yang
     * disinkronkan dari file events.json — ikut terkena masalah dua sumber data,
     * dan kenyataannya kosong di semua event sehingga seluruh pembayaran jatuh ke
     * rekening contoh yang di-hardcode di controller.
     *
     * Pesanan menyimpan salinan nomor rekening yang dipakai saat memesan, supaya
     * riwayat tetap benar walau super admin mengganti rekening di kemudian hari.
     */
    public function up(): void
    {
        Schema::create('event_payment_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('bank_name');
            $table->string('account_number');
            $table->string('account_holder');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['event_id', 'is_active']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('payment_account_id')->nullable()->after('payment_method')
                ->constrained('event_payment_accounts')->nullOnDelete();
            $table->string('payment_account_number')->nullable()->after('payment_account_id');
            $table->string('payment_account_holder')->nullable()->after('payment_account_number');
        });

        $this->backfill();

        // Kolom lama tidak dipakai lagi — rekening kini punya tabel sendiri.
        if (Schema::hasColumn('events', 'payment_methods')) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropColumn('payment_methods');
            });
        }
    }

    /**
     * Pindahkan rekening dari kolom JSON lama ke tabel baru, lalu cocokkan
     * pesanan lama ke rekening yang namanya sesuai.
     */
    private function backfill(): void
    {
        if (! Schema::hasColumn('events', 'payment_methods')) {
            return;
        }

        foreach (DB::table('events')->whereNotNull('payment_methods')->get() as $event) {
            $methods = json_decode($event->payment_methods, true);
            if (! is_array($methods)) {
                continue;
            }

            foreach (array_values($methods) as $i => $method) {
                if (empty($method['name'])) {
                    continue;
                }

                $accountId = DB::table('event_payment_accounts')->insertGetId([
                    'event_id' => $event->id,
                    'bank_name' => $method['name'],
                    'account_number' => $method['account_number'] ?? '-',
                    'account_holder' => $method['account_holder'] ?? '-',
                    'is_active' => true,
                    'sort_order' => ($i + 1) * 10,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('orders')
                    ->where('event_id', $event->id)
                    ->where('payment_method', $method['name'])
                    ->whereNull('payment_account_id')
                    ->update([
                        'payment_account_id' => $accountId,
                        'payment_account_number' => $method['account_number'] ?? null,
                        'payment_account_holder' => $method['account_holder'] ?? null,
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->text('payment_methods')->nullable()->after('quota');
        });

        foreach (DB::table('event_payment_accounts')->orderBy('sort_order')->get()->groupBy('event_id') as $eventId => $accounts) {
            DB::table('events')->where('id', $eventId)->update([
                'payment_methods' => json_encode($accounts->map(fn ($a) => [
                    'name' => $a->bank_name,
                    'account_number' => $a->account_number,
                    'account_holder' => $a->account_holder,
                ])->values()),
            ]);
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['payment_account_id']);
            $table->dropColumn(['payment_account_id', 'payment_account_number', 'payment_account_holder']);
        });

        Schema::dropIfExists('event_payment_accounts');
    }
};
