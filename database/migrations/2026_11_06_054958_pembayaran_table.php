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
    Schema::create('pembayaran', function (Blueprint $table) {
        $table->string('id_pembayaran', 36)->primary();
        $table->string('id_pesanan', 36);

        $table->enum('metode_pembayaran', [
            'dana',
            'gopay',
            'bank'
        ]);

        $table->decimal('jumlah', 12, 2);

        $table->enum('status_pembayaran', [
            'pending',
            'paid',
            'failed',
            'refunded'
        ])->default('pending');

        $table->enum('bukti_pembayaran', [
            'accept',
            'decline'
        ])->nullable();

        $table->string('image_bukti', 255)->nullable();
        $table->timestamp('paid_at')->nullable();
        $table->string('id_admin_verifikator', 36)->nullable();
        $table->timestamp('verified_at')->nullable();
        $table->timestamps();

        $table->foreign('id_pesanan')
            ->references('id_pesanan')
            ->on('pesanan');

        $table->foreign('id_admin_verifikator')
            ->references('id_user')
            ->on('users');
    });
}

public function down(): void
{
    Schema::dropIfExists('pembayaran');
}

};
