<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table): void {
                $table->string('id_produk', 36)->primary();
                $table->string('id_kategori', 36);
                $table->string('name_produk', 150);
                $table->text('deskripsi_produk')->nullable();
                $table->decimal('harga_produk', 12, 2);
                $table->integer('stock_produk')->default(0);
                $table->string('image', 255)->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->foreign('id_kategori')->references('id_kategori')->on('kategori');
            });

            return;
        }

        if (! Schema::hasColumn('products', 'deleted_at')) {
            Schema::table('products', function (Blueprint $table): void {
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
