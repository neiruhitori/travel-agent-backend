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
        Schema::create('paymentsub', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->constrained('pengajuan');
            $table->timestamp('paid_at')->nullable();
            $table->decimal('amount_paid', 15, 2);
            $table->enum('method', ['transfer_bank', 'cash', 'credit_card', 'debit_card', 'e_wallet']);
            $table->string('path_file')->nullable(); // Path untuk bukti pembayaran
            $table->string('verified_by')->nullable(); // Status saat diverifikasi
            $table->timestamp('verified_at')->nullable(); // Waktu diverifikasi
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paymentsub');
    }
};
