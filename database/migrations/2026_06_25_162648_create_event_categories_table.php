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
        Schema::create('event_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('code');
            $table->string('bib_code');
            $table->integer('price')->default(0);
            $table->timestamps();
        });

        // Seed default categories for existing events
        try {
            $events = \DB::table('events')->get();
            foreach ($events as $event) {
                \DB::table('event_categories')->insert([
                    [
                        'event_id' => $event->id,
                        'name' => '3K Fun Walk',
                        'code' => '3K',
                        'bib_code' => 'FW',
                        'price' => 100000,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'event_id' => $event->id,
                        'name' => '5K Night Run',
                        'code' => '5K',
                        'bib_code' => 'NR',
                        'price' => 150000,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'event_id' => $event->id,
                        'name' => '10K Challenger',
                        'code' => '10K',
                        'bib_code' => 'CH',
                        'price' => 250000,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                ]);
            }
        } catch (\Throwable $e) {
            // Ignore if any errors occur during seeding
        }
    }
 
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_categories');
    }
};
