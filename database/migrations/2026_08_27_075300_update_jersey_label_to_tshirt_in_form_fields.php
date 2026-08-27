<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Update existing core field labels that were renamed.
     * "Ukuran Jersey" → "Ukuran T-Shirt"
     */
    public function up(): void
    {
        // Update label on all existing event_form_fields records where key = jersey_size
        // and label is still the old value (so we don't overwrite custom admin edits)
        DB::table('event_form_fields')
            ->where('key', 'jersey_size')
            ->where('is_core', true)
            ->where('label', 'Ukuran Jersey')
            ->update(['label' => 'Ukuran T-Shirt']);
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        DB::table('event_form_fields')
            ->where('key', 'jersey_size')
            ->where('is_core', true)
            ->where('label', 'Ukuran T-Shirt')
            ->update(['label' => 'Ukuran Jersey']);
    }
};
