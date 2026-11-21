<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pembayaran')) {
            Schema::create('pembayaran', function (Blueprint $table): void {
                $table->string('id_pembayaran', 36)->primary();
                $table->string('id_pesanan', 36);
                $table->enum('metode_pembayaran', ['dana', 'gopay', 'bank', 'shopee_pay', 'bri', 'bca', 'tunai']);
                $table->decimal('jumlah', 12, 2);
                $table->enum('status_pembayaran', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
                $table->enum('bukti_pembayaran', ['accept', 'decline'])->nullable();
                $table->string('image_bukti', 255)->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->string('id_admin_verifikator', 36)->nullable();
                $table->timestamp('verified_at')->nullable();
                $table->string('id_pengembalian_dana', 36)->nullable()->unique();
                $table->string('bukti_pengembalian')->nullable();
                $table->string('id_admin_pemroses', 36)->nullable();
                $table->timestamp('processed_at')->nullable();
                $table->timestamps();
                $table->foreign('id_pesanan')->references('id_pesanan')->on('pesanan');
                $table->foreign('id_admin_verifikator')->references('id_user')->on('users');
                $table->foreign('id_admin_pemroses')->references('id_user')->on('users');
            });

            return;
        }

        Schema::table('pembayaran', function (Blueprint $table): void {
            $table->enum('metode_pembayaran', ['dana', 'gopay', 'bank', 'shopee_pay', 'bri', 'bca', 'tunai'])->change();
        });

        if (! Schema::hasColumn('pembayaran', 'image_bukti')) {
            Schema::table('pembayaran', function (Blueprint $table): void {
                $table->string('image_bukti', 255)->nullable();
            });
        }

        if (! Schema::hasColumn('pembayaran', 'id_pengembalian_dana')) {
            Schema::table('pembayaran', function (Blueprint $table): void {
                $table->string('id_pengembalian_dana', 36)->nullable()->unique();
            });
        }

        if (! Schema::hasColumn('pembayaran', 'bukti_pengembalian')) {
            Schema::table('pembayaran', function (Blueprint $table): void {
                $table->string('bukti_pengembalian')->nullable();
            });
        }

        if (! Schema::hasColumn('pembayaran', 'id_admin_pemroses')) {
            Schema::table('pembayaran', function (Blueprint $table): void {
                $table->string('id_admin_pemroses', 36)->nullable();
                $table->foreign('id_admin_pemroses')->references('id_user')->on('users');
            });
        }

        if (! Schema::hasColumn('pembayaran', 'processed_at')) {
            Schema::table('pembayaran', function (Blueprint $table): void {
                $table->timestamp('processed_at')->nullable();
            });
        }

        if (Schema::hasTable('pengembalian_dana')) {
            DB::table('pengembalian_dana')->orderBy('id_pengembalian_dana')->chunk(100, function ($refunds): void {
                foreach ($refunds as $refund) {
                    DB::table('pembayaran')
                        ->where('id_pembayaran', $refund->id_pembayaran)
                        ->update([
                            'id_pengembalian_dana' => $refund->id_pengembalian_dana,
                            'bukti_pengembalian' => $refund->bukti_pengembalian,
                            'id_admin_pemroses' => $refund->id_admin_pemroses,
                            'processed_at' => $refund->processed_at,
                        ]);
                }
            });

            Schema::drop('pengembalian_dana');
        }

        foreach (['sessions', 'password_reset_tokens', 'cache_locks', 'cache', 'job_batches', 'failed_jobs', 'jobs'] as $table) {
            Schema::dropIfExists($table);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
