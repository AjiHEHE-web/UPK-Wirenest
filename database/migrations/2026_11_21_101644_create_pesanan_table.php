<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pesanan')) {
            Schema::create('pesanan', function (Blueprint $table): void {
                $table->string('id_pesanan', 36)->primary();
                $table->string('id_user', 36);
                $table->timestamp('tanggal_pemesanan')->nullable();
                $table->decimal('total_harga', 12, 2);
                $table->enum('status_pesanan', ['pending', 'processing', 'shipped', 'completed', 'cancelled'])->default('pending');
                $table->timestamp('confirmed_at')->nullable();
                $table->text('alamat_pengiriman')->nullable();
                $table->text('catatan')->nullable();
                $table->boolean('stock_reserved')->default(false);
                $table->timestamp('created_at')->nullable();
                $table->foreign('id_user')->references('id_user')->on('users');
            });

            return;
        }

        if (! Schema::hasColumn('pesanan', 'alamat_pengiriman')) {
            Schema::table('pesanan', function (Blueprint $table): void {
                $table->text('alamat_pengiriman')->nullable();
            });
        }

        if (! Schema::hasColumn('pesanan', 'catatan')) {
            Schema::table('pesanan', function (Blueprint $table): void {
                $table->text('catatan')->nullable();
            });
        }

        if (! Schema::hasColumn('pesanan', 'stock_reserved')) {
            Schema::table('pesanan', function (Blueprint $table): void {
                $table->boolean('stock_reserved')->default(false);
            });
        }

        DB::table('pesanan')->where('status_pesanan', 'confirmed')->update(['status_pesanan' => 'pending']);

        Schema::table('pesanan', function (Blueprint $table): void {
            $table->enum('status_pesanan', ['pending', 'processing', 'shipped', 'completed', 'cancelled'])->default('pending')->change();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};
