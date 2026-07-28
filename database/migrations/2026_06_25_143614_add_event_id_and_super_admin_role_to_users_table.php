<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('event_id')->nullable()->constrained('events')->onDelete('set null');
        });

        // Seed Super Admin
        \Illuminate\Support\Facades\DB::table('users')->insertOrIgnore([
            'name' => 'Super Administrator',
            'email' => 'superadmin@setiket.com',
            'password' => \Illuminate\Support\Facades\Hash::make('admin123'),
            'role' => 'super_admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['event_id']);
            $table->dropColumn('event_id');
        });
    }
};
