<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('favorites')) {
            return;
        }

        Schema::create('favorites', function (Blueprint $table): void {
            $table->string('id_favorit', 36)->primary();
            $table->string('id_user', 36);
            $table->string('id_produk', 36);
            $table->timestamp('created_at')->nullable();
            $table->unique(['id_user', 'id_produk']);
            $table->foreign('id_user')->references('id_user')->on('users');
            $table->foreign('id_produk')->references('id_produk')->on('products');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
