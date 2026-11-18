<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayaran', function (Blueprint $table): void {
            $table->enum('metode_pembayaran', [
                'dana',
                'gopay',
                'bank',
                'shopee_pay',
                'bri',
                'bca',
                'tunai',
            ])->change();
        });
    }

    public function down(): void
    {
        Schema::table('pembayaran', function (Blueprint $table): void {
            $table->enum('metode_pembayaran', ['dana', 'gopay', 'bank'])->change();
        });
    }
};
