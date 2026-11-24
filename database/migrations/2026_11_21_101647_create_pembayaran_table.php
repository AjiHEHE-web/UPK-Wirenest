<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pembayaran')) {
            Schema::create('pembayaran', function (Blueprint $table): void {
                $table->string('id_pembayaran', 36)->primary();
                $table->string('id_pesanan', 36);
                $table->enum('metode_pembayaran', ['dana', 'gopay', 'bank', 'shopee_pay', 'bri', 'bca']);
                $table->decimal('jumlah', 12, 2);
                $table->enum('status_pembayaran', ['pending', 'paid', 'failed'])->default('pending');
                $table->enum('bukti_pembayaran', ['accept', 'decline'])->nullable();
                $table->string('image_bukti', 255)->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->string('id_admin_verifikator', 36)->nullable();
                $table->timestamp('verified_at')->nullable();
                $table->timestamps();
                $table->foreign('id_pesanan')->references('id_pesanan')->on('pesanan');
                $table->foreign('id_admin_verifikator')->references('id_user')->on('users');
            });

            return;
        }

        Schema::table('pembayaran', function (Blueprint $table): void {
            $table->enum('metode_pembayaran', ['dana', 'gopay', 'bank', 'shopee_pay', 'bri', 'bca'])->change();
        });

        if (! Schema::hasColumn('pembayaran', 'image_bukti')) {
            Schema::table('pembayaran', function (Blueprint $table): void {
                $table->string('image_bukti', 255)->nullable();
            });
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
