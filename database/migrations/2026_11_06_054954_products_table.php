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
    Schema::create('products', function (Blueprint $table) {
        $table->string('id_produk', 36)->primary();
        $table->string('id_kategori', 36);
        $table->string('name_produk', 150);
        $table->text('deskripsi_produk')->nullable();
        $table->decimal('harga_produk', 12, 2);
        $table->integer('stock_produk')->default(0);
        $table->string('image', 255)->nullable();
        $table->timestamps();

        $table->foreign('id_kategori')
            ->references('id_kategori')
            ->on('kategori');
    });
}

public function down(): void
{
    Schema::dropIfExists('products');
}

};
