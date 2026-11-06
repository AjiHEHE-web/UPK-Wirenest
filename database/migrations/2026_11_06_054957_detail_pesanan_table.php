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
    Schema::create('detail_pesanan', function (Blueprint $table) {
        $table->string('id_detail_pesanan', 36)->primary();
        $table->string('id_pesanan', 36);
        $table->string('id_produk', 36);
        $table->decimal('harga_satuan', 12, 2);
        $table->integer('jumlah');
        $table->decimal('subtotal', 12, 2);

        $table->foreign('id_pesanan')
            ->references('id_pesanan')
            ->on('pesanan');

        $table->foreign('id_produk')
            ->references('id_produk')
            ->on('products');
    });
}

public function down(): void
{
    Schema::dropIfExists('detail_pesanan');
}

};
