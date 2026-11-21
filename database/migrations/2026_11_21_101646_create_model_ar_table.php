<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('model_ar')) {
            return;
        }

        Schema::create('model_ar', function (Blueprint $table): void {
            $table->string('id_model_ar', 36)->primary();
            $table->string('id_produk', 36)->nullable()->unique();
            $table->string('nama_model', 150);
            $table->string('image_ar', 255);
            $table->timestamp('created_at')->nullable();
            $table->foreign('id_produk')->references('id_produk')->on('products')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('model_ar');
    }
};
