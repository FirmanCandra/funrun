<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->runningInConsole()) {
            return;
        }

        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('users') && !\Illuminate\Support\Facades\Schema::hasColumn('users', 'plain_password')) {
                \Illuminate\Support\Facades\Schema::table('users', function ($table) {
                    $table->string('plain_password')->nullable()->after('password');
                });
            }
        } catch (\Throwable $e) {
            // Abaikan jika database belum siap
        }
    }
}
