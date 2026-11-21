<?php

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

test('admins see only cancelled and approved payments with uploaded proof in the refund queue', function () {
    $admin = User::factory()->create();
    $admin->forceFill(['role' => 'admin'])->save();
    $customer = User::factory()->create(['nama' => 'Pelanggan Refund']);

    $eligibleOrder = Order::factory()->create([
        'id_user' => $customer->id_user,
        'status_pesanan' => 'cancelled',
    ]);
    Payment::factory()->create([
        'id_pesanan' => $eligibleOrder->id_pesanan,
        'status_pembayaran' => 'paid',
        'bukti_pembayaran' => 'accept',
        'image_bukti' => 'payments/customer-proof.jpg',
    ]);

    $pendingOrder = Order::factory()->create([
        'id_user' => $customer->id_user,
        'status_pesanan' => 'cancelled',
    ]);
    Payment::factory()->create([
        'id_pesanan' => $pendingOrder->id_pesanan,
        'status_pembayaran' => 'pending',
        'bukti_pembayaran' => null,
        'image_bukti' => 'payments/pending-customer-proof.jpg',
    ]);

    $pendingOrderWithoutProof = Order::factory()->create([
        'id_user' => $customer->id_user,
        'status_pesanan' => 'cancelled',
    ]);
    Payment::factory()->create([
        'id_pesanan' => $pendingOrderWithoutProof->id_pesanan,
        'status_pembayaran' => 'pending',
        'bukti_pembayaran' => null,
        'image_bukti' => null,
    ]);

    $activeOrder = Order::factory()->create([
        'id_user' => $customer->id_user,
        'status_pesanan' => 'processing',
    ]);
    Payment::factory()->create([
        'id_pesanan' => $activeOrder->id_pesanan,
        'status_pembayaran' => 'paid',
        'bukti_pembayaran' => 'accept',
        'image_bukti' => 'payments/active-order-proof.jpg',
    ]);

    $this->actingAs($admin)
        ->get('/admin/refunds')
        ->assertOk()
        ->assertSee($eligibleOrder->id_pesanan)
        ->assertDontSee($pendingOrder->id_pesanan)
        ->assertDontSee($pendingOrderWithoutProof->id_pesanan)
        ->assertDontSee($activeOrder->id_pesanan);
});

test('regular users cannot access the refund queue', function () {
    $this->actingAs(User::factory()->create())
        ->get('/admin/refunds')
        ->assertForbidden();
});

test('guests are redirected to login when opening the refund queue', function () {
    $this->get('/admin/refunds')
        ->assertRedirect(route('login'));
});

test('admins can record a manual refund without overwriting the original payment verification', function () {
    Storage::fake('local');
    $admin = User::factory()->create();
    $admin->forceFill(['role' => 'admin'])->save();
    $verifier = User::factory()->create();
    $verifier->forceFill(['role' => 'admin'])->save();
    $customer = User::factory()->create();
    $verifiedAt = now()->subDay();
    $order = Order::factory()->create([
        'id_user' => $customer->id_user,
        'total_harga' => 85000,
        'status_pesanan' => 'cancelled',
    ]);
    $payment = Payment::factory()->create([
        'id_pesanan' => $order->id_pesanan,
        'jumlah' => 125000,
        'status_pembayaran' => 'paid',
        'bukti_pembayaran' => 'accept',
        'image_bukti' => 'payments/customer-proof.jpg',
        'id_admin_verifikator' => $verifier->id_user,
        'verified_at' => $verifiedAt,
        'paid_at' => $verifiedAt,
    ]);

    $this->actingAs($admin)
        ->post("/admin/refunds/{$payment->id_pembayaran}", [
            'bukti_pengembalian' => UploadedFile::fake()->image('refund.png'),
            'jumlah' => '1.00',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('pembayaran', [
        'id_pembayaran' => $payment->id_pembayaran,
        'jumlah' => '125000.00',
        'id_admin_pemroses' => $admin->id_user,
    ]);
    $this->assertDatabaseHas('pembayaran', [
        'id_pembayaran' => $payment->id_pembayaran,
        'status_pembayaran' => 'refunded',
        'bukti_pembayaran' => 'accept',
        'image_bukti' => 'payments/customer-proof.jpg',
        'id_admin_verifikator' => $verifier->id_user,
        'verified_at' => $verifiedAt,
        'paid_at' => $verifiedAt,
    ]);

    $refund = Payment::query()->whereKey($payment->id_pembayaran)->firstOrFail();
    expect($refund->bukti_pengembalian)->not->toBeNull();

    Storage::disk('local')->assertExists($refund->bukti_pengembalian);
    $this->get('/admin/refunds')
        ->assertOk()
        ->assertSee('Riwayat pengembalian')
        ->assertSee($customer->nama);

    $this->post("/admin/refunds/{$payment->id_pembayaran}", [
        'bukti_pengembalian' => UploadedFile::fake()->image('duplicate-refund.png'),
    ])->assertSessionHasErrors('bukti_pengembalian');

    $this->assertDatabaseCount('pembayaran', 1);
    $this->get("/admin/refunds/{$refund->id_pembayaran}/proof")->assertOk();
});

test('admins cannot refund a cancelled payment before an admin approves it', function () {
    Storage::fake('local');
    $admin = User::factory()->create();
    $admin->forceFill(['role' => 'admin'])->save();
    $customer = User::factory()->create();
    $order = Order::factory()->create([
        'id_user' => $customer->id_user,
        'status_pesanan' => 'cancelled',
    ]);
    $payment = Payment::factory()->create([
        'id_pesanan' => $order->id_pesanan,
        'jumlah' => 85000,
        'status_pembayaran' => 'pending',
        'bukti_pembayaran' => null,
        'image_bukti' => 'payments/unverified-customer-proof.jpg',
        'id_admin_verifikator' => null,
        'verified_at' => null,
        'paid_at' => null,
    ]);

    $this->actingAs($admin)
        ->post("/admin/refunds/{$payment->id_pembayaran}", [
            'bukti_pengembalian' => UploadedFile::fake()->image('refund.png'),
        ])
        ->assertSessionHasErrors('bukti_pengembalian');

    $this->assertDatabaseMissing('pembayaran', [
        'id_pembayaran' => $payment->id_pembayaran,
        'bukti_pengembalian' => 'unexpected-proof.jpg',
    ]);
    $this->assertDatabaseHas('pembayaran', [
        'id_pembayaran' => $payment->id_pembayaran,
        'status_pembayaran' => 'pending',
    ]);
});

test('refund proof must be an allowed image or PDF file', function () {
    Storage::fake('local');
    $admin = User::factory()->create();
    $admin->forceFill(['role' => 'admin'])->save();
    $order = Order::factory()->create(['status_pesanan' => 'cancelled']);
    $payment = Payment::factory()->create([
        'id_pesanan' => $order->id_pesanan,
        'status_pembayaran' => 'paid',
        'bukti_pembayaran' => 'accept',
        'image_bukti' => 'payments/customer-proof.jpg',
    ]);

    $this->actingAs($admin)
        ->post("/admin/refunds/{$payment->id_pembayaran}", [
            'bukti_pengembalian' => UploadedFile::fake()->create('refund.txt', 10, 'text/plain'),
        ])
        ->assertSessionHasErrors('bukti_pengembalian');

    $this->assertDatabaseCount('pembayaran', 1);
});

test('admins cannot refund failed payments, active orders, or cancelled orders without customer proof', function () {
    Storage::fake('local');
    $admin = User::factory()->create();
    $admin->forceFill(['role' => 'admin'])->save();

    $cancelledOrder = Order::factory()->create(['status_pesanan' => 'cancelled']);
    $failedPayment = Payment::factory()->create([
        'id_pesanan' => $cancelledOrder->id_pesanan,
        'status_pembayaran' => 'failed',
        'image_bukti' => 'payments/proof.jpg',
    ]);

    $activeOrder = Order::factory()->create(['status_pesanan' => 'processing']);
    $paidPayment = Payment::factory()->create([
        'id_pesanan' => $activeOrder->id_pesanan,
        'status_pembayaran' => 'paid',
        'bukti_pembayaran' => 'accept',
        'image_bukti' => 'payments/proof.jpg',
    ]);
    $missingProofOrder = Order::factory()->create(['status_pesanan' => 'cancelled']);
    $missingProofPayment = Payment::factory()->create([
        'id_pesanan' => $missingProofOrder->id_pesanan,
        'status_pembayaran' => 'paid',
        'bukti_pembayaran' => 'accept',
        'image_bukti' => null,
    ]);

    $this->actingAs($admin)
        ->post("/admin/refunds/{$failedPayment->id_pembayaran}", [
            'bukti_pengembalian' => UploadedFile::fake()->image('refund.png'),
        ])
        ->assertSessionHasErrors('bukti_pengembalian');

    $this->post("/admin/refunds/{$paidPayment->id_pembayaran}", [
        'bukti_pengembalian' => UploadedFile::fake()->image('refund.png'),
    ])->assertSessionHasErrors('bukti_pengembalian');

    $this->post("/admin/refunds/{$missingProofPayment->id_pembayaran}", [
        'bukti_pengembalian' => UploadedFile::fake()->image('refund.png'),
    ])->assertSessionHasErrors('bukti_pengembalian');
});

test('database schema contains only application tables from the diagram', function () {
    expect(count(glob(database_path('migrations/*.php'))))->toBe(8);

    foreach (['users', 'kategori', 'products', 'favorites', 'pesanan', 'detail_pesanan', 'pembayaran', 'model_ar'] as $table) {
        expect(Schema::hasTable($table))->toBeTrue();
    }

    expect(Schema::hasTable('pengembalian_dana'))->toBeFalse();
    expect(Schema::hasTable('sessions'))->toBeFalse();
    expect(Schema::hasTable('cache'))->toBeFalse();
    expect(Schema::hasTable('cache_locks'))->toBeFalse();
    expect(Schema::hasTable('jobs'))->toBeFalse();
    expect(Schema::hasTable('job_batches'))->toBeFalse();
    expect(Schema::hasTable('failed_jobs'))->toBeFalse();
    expect(Schema::hasTable('password_reset_tokens'))->toBeFalse();
});
