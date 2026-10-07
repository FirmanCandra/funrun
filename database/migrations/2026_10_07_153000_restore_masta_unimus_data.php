<?php

use Illuminate\Database\Migrations\Migration;
use App\Services\RestoreService;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (app()->runningUnitTests()) {
            return;
        }
        RestoreService::ensureRestored(true);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
