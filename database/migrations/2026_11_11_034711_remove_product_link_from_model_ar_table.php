<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('model_ar', function (Blueprint $table) {
            $table->string('id_produk', 36)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::table('model_ar')->whereNull('id_produk')->exists()) {
            throw new RuntimeException('Model AR tanpa produk harus dihubungkan ke produk sebelum migrasi ini dapat dibatalkan.');
        }

        Schema::table('model_ar', function (Blueprint $table) {
            $table->string('id_produk', 36)->nullable(false)->change();
        });
    }
};
