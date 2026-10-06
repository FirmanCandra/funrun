<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Jalankan AdminSeeder (Super Admin) & RealisticEventsSeeder untuk event
        $this->call(AdminSeeder::class);
        $this->call(RealisticEventsSeeder::class);
    }
}
