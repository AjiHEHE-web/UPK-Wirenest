<?php

use App\Models\User;
use Illuminate\Support\Facades\Schema;

test('refund routes are unavailable and the admin navigation is removed', function () {
    $this->get('/admin/refunds')->assertNotFound();
    $this->post('/admin/refunds/payment-id')->assertNotFound();
    $this->get('/admin/refunds/payment-id/proof')->assertNotFound();

    $admin = User::factory()->create();
    $admin->forceFill(['role' => 'admin'])->save();

    $this->actingAs($admin)
        ->get('/admin/refunds')
        ->assertNotFound();

    $this->actingAs($admin)
        ->get('/admin/dashboard')
        ->assertOk()
        ->assertDontSee('Pengembalian Dana');
});

test('database schema contains only application tables from the diagram', function () {
    expect(count(glob(database_path('migrations/*.php'))))->toBe(8);

    foreach (['users', 'kategori', 'products', 'favorites', 'pesanan', 'detail_pesanan', 'pembayaran', 'model_ar'] as $table) {
        expect(Schema::hasTable($table))->toBeTrue();
    }

    expect(Schema::hasTable('pengembalian_dana'))->toBeFalse();
    expect(Schema::hasColumn('pembayaran', 'id_pengembalian_dana'))->toBeFalse();
    expect(Schema::hasColumn('pembayaran', 'bukti_pengembalian'))->toBeFalse();
    expect(Schema::hasColumn('pembayaran', 'id_admin_pemroses'))->toBeFalse();
    expect(Schema::hasColumn('pembayaran', 'processed_at'))->toBeFalse();
    expect(Schema::hasTable('sessions'))->toBeFalse();
    expect(Schema::hasTable('cache'))->toBeFalse();
    expect(Schema::hasTable('cache_locks'))->toBeFalse();
    expect(Schema::hasTable('jobs'))->toBeFalse();
    expect(Schema::hasTable('job_batches'))->toBeFalse();
    expect(Schema::hasTable('failed_jobs'))->toBeFalse();
    expect(Schema::hasTable('password_reset_tokens'))->toBeFalse();
});
