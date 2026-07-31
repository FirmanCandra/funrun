<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CleanDemoDataSeeder extends Seeder
{
    /**
     * Menghapus semua data demo/contoh untuk persiapan hosting production.
     * Super Admin (role: super_admin) TIDAK akan dihapus.
     */
    public function run(): void
    {
        $this->command->info('🧹 Mulai membersihkan data demo...');

        // Matikan foreign key check sementara agar bisa hapus data berantai
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $schema = DB::getSchemaBuilder();

        // 1. Hapus semua data transaksi & peserta
        if ($schema->hasTable('payments')) {
            DB::table('payments')->truncate();
            $this->command->info('✅ Tabel payments dibersihkan.');
        }

        if ($schema->hasTable('participants')) {
            DB::table('participants')->truncate();
            $this->command->info('✅ Tabel participants dibersihkan.');
        }

        if ($schema->hasTable('orders')) {
            DB::table('orders')->truncate();
            $this->command->info('✅ Tabel orders dibersihkan.');
        }

        if ($schema->hasTable('tickets')) {
            DB::table('tickets')->truncate();
            $this->command->info('✅ Tabel tickets dibersihkan.');
        }

        // 2. Hapus semua event (beserta kategori & form fields)
        if ($schema->hasTable('event_form_fields')) {
            DB::table('event_form_fields')->truncate();
            $this->command->info('✅ Tabel event_form_fields dibersihkan.');
        }

        if ($schema->hasTable('event_payment_accounts')) {
            DB::table('event_payment_accounts')->truncate();
            $this->command->info('✅ Tabel event_payment_accounts dibersihkan.');
        }

        if ($schema->hasTable('event_categories')) {
            DB::table('event_categories')->truncate();
            $this->command->info('✅ Tabel event_categories dibersihkan.');
        }

        if ($schema->hasTable('events')) {
            DB::table('events')->truncate();
            $this->command->info('✅ Tabel events dibersihkan.');
        }

        // 3. Hapus semua user KECUALI super_admin
        if ($schema->hasTable('users')) {
            DB::table('users')
                ->where('role', '!=', \App\Models\User::ROLE_SUPER_ADMIN)
                ->delete();
            $this->command->info('✅ Data user (non-super_admin) dihapus.');
        }

        // Aktifkan kembali foreign key check
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 4. Pastikan Super Admin masih ada (buat ulang jika tidak ada)
        \App\Models\User::firstOrCreate(
            ['email' => 'superadmin@setiket.com'],
            [
                'name'     => 'Super Administrator',
                'password' => bcrypt('admin123'),
                'role'     => \App\Models\User::ROLE_SUPER_ADMIN,
            ]
        );
        $this->command->info('✅ Super Admin dipastikan masih ada.');

        $this->command->info('');
        $this->command->info('🎉 Data demo berhasil dibersihkan! Database siap untuk production.');
        $this->command->info('   Login Super Admin: superadmin@setiket.com / admin123');
        $this->command->warn('   ⚠️  Segera ganti password Super Admin setelah login!');
    }
}
