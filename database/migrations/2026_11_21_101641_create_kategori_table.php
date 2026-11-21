<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('kategori')) {
            return;
        }

        Schema::create('kategori', function (Blueprint $table): void {
            $table->string('id_kategori', 36)->primary();
            $table->string('name', 100);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori');
    }
};
