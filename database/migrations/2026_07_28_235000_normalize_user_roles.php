<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Menyeragamkan penamaan role menjadi: user, admin, super_admin.
     * Sebelumnya peserta memakai role 'participant'.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('user')->change();
        });

        DB::table('users')->where('role', 'participant')->update(['role' => 'user']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('users')->where('role', 'user')->update(['role' => 'participant']);

        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('participant')->change();
        });
    }
};
