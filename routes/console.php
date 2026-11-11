<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('data:wipe-transactions', function () {
    $this->info('Membersihkan seluruh data pesanan, tiket, dan peserta...');
    \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    \App\Models\Ticket::truncate();
    \App\Models\Participant::truncate();
    \App\Models\Order::truncate();
    \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    try {
        $files = \Illuminate\Support\Facades\Storage::disk('public')->files('proofs');
        if (!empty($files)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($files);
        }
    } catch (\Throwable $e) {
        // ignore
    }

    $this->info('Sukses! Seluruh data transaksi, tiket, dan peserta telah dikosongkan.');
})->purpose('Hapus seluruh pesanan, tiket, dan peserta secara permanen');
