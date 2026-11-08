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
        Schema::create('pengembalian_dana', function (Blueprint $table) {
            $table->string('id_pengembalian_dana', 36)->primary();
            $table->string('id_pembayaran', 36)->unique();
            $table->decimal('jumlah', 12, 2);
            $table->string('bukti_pengembalian');
            $table->string('id_admin_pemroses', 36);
            $table->timestamp('processed_at');
            $table->timestamps();

            $table->foreign('id_pembayaran')
                ->references('id_pembayaran')
                ->on('pembayaran');

            $table->foreign('id_admin_pemroses')
                ->references('id_user')
                ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengembalian_dana');
    }
};
