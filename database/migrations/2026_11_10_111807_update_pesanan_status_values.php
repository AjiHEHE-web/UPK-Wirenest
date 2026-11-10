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
        DB::table('pesanan')
            ->where('status_pesanan', 'confirmed')
            ->update(['status_pesanan' => 'pending']);

        Schema::table('pesanan', function (Blueprint $table): void {
            $table->enum('status_pesanan', [
                'pending',
                'processing',
                'shipped',
                'completed',
                'cancelled',
            ])->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table): void {
            $table->enum('status_pesanan', [
                'pending',
                'confirmed',
                'processing',
                'shipped',
                'completed',
                'cancelled',
            ])->default('pending')->change();
        });

        DB::table('pesanan')
            ->where('status_pesanan', 'pending')
            ->whereNotNull('confirmed_at')
            ->update(['status_pesanan' => 'confirmed']);
    }
};
