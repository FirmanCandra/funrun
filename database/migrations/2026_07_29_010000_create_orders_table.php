<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Satu pesanan bisa berisi banyak tiket dengan satu bukti transfer.
     *
     * Sebelumnya pembayaran menempel per tiket (tabel `payments`), sehingga
     * pembelian 3 tiket menghasilkan 3 baris pembayaran yang harus disetujui
     * admin satu per satu. Tabel `orders` menggantikannya sebagai satu-satunya
     * catatan pembayaran; tiket menunjuk ke pesanan lewat `tickets.order_id`.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->string('payment_method')->nullable();
            // waiting_verification -> paid | rejected
            $table->string('payment_status')->default('waiting_verification');
            $table->string('proof_of_payment')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            // Admin event menyaring antriannya lewat kolom ini.
            $table->index(['event_id', 'payment_status']);
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('order_id')->nullable()->after('participant_id')
                ->constrained()->onDelete('cascade');
        });

        $this->backfillFromPayments();

        Schema::dropIfExists('payments');
    }

    /**
     * Pindahkan pembayaran lama menjadi pesanan berisi satu tiket,
     * supaya tidak ada data yang hilang saat tabel `payments` dibuang.
     */
    private function backfillFromPayments(): void
    {
        if (! Schema::hasTable('payments')) {
            return;
        }

        $payments = DB::table('payments')
            ->join('tickets', 'tickets.id', '=', 'payments.ticket_id')
            ->join('participants', 'participants.id', '=', 'tickets.participant_id')
            ->select(
                'payments.*',
                'tickets.id as ticket_id_ref',
                'participants.user_id',
                'participants.event_id'
            )
            ->get();

        foreach ($payments as $payment) {
            $orderId = DB::table('orders')->insertGetId([
                'order_code' => 'ORD-LEGACY-'.str_pad((string) $payment->id, 6, '0', STR_PAD_LEFT),
                'user_id' => $payment->user_id,
                'event_id' => $payment->event_id,
                'total_amount' => $payment->amount,
                'payment_method' => $payment->payment_method,
                'payment_status' => $payment->payment_status,
                'proof_of_payment' => $payment->proof_of_payment ?? null,
                'created_at' => $payment->created_at,
                'updated_at' => $payment->updated_at,
            ]);

            DB::table('tickets')->where('id', $payment->ticket_id_ref)->update(['order_id' => $orderId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->default('pending');
            $table->string('proof_of_payment')->nullable();
            $table->timestamps();
        });

        // Kembalikan satu baris pembayaran untuk tiap tiket dalam pesanan.
        $tickets = DB::table('tickets')
            ->join('orders', 'orders.id', '=', 'tickets.order_id')
            ->select('tickets.id as ticket_id', 'orders.*')
            ->get();

        foreach ($tickets as $row) {
            DB::table('payments')->insert([
                'ticket_id' => $row->ticket_id,
                'amount' => $row->total_amount,
                'payment_method' => $row->payment_method,
                'payment_status' => $row->payment_status,
                'proof_of_payment' => $row->proof_of_payment,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ]);
        }

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropColumn('order_id');
        });

        Schema::dropIfExists('orders');
    }
};
