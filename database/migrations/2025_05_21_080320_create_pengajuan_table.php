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
        Schema::create('pengajuan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('institution');
            $table->string('applicant');
            $table->string('email');
            $table->foreignId('destination_id')->constrained('destinations')->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->date('departure_date');
            $table->date('return_date');
            $table->unsignedInteger('participants');
            $table->text('notes')->nullable();
            $table->enum('status', [
                'menunggu_konfirmasi',
                'menunggu_persetujuan',
                'disetujui',
                'dalam_perjalanan',
                'menunggu_pembayaran',
                'menunggu_verifikasi_pembayaran',
                'pembayaran_ditolak',
                'lunas',
                'ditolak',
            ])->default('menunggu_konfirmasi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan');
    }
};
