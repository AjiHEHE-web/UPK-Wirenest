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
    Schema::create('pesanan', function (Blueprint $table) {
        $table->string('id_pesanan', 36)->primary();
        $table->string('id_user', 36);
        $table->timestamp('tanggal_pemesanan')->nullable();
        $table->decimal('total_harga', 12, 2);

        $table->enum('status_pesanan', [
            'pending',
            'confirmed',
            'processing',
            'shipped',
            'completed',
            'cancelled'
        ])->default('pending');

        $table->timestamp('confirmed_at')->nullable();
        $table->timestamp('created_at')->nullable();

        $table->foreign('id_user')
            ->references('id_user')
            ->on('users');
    });
}

public function down(): void
{
    Schema::dropIfExists('pesanan');
}

};
