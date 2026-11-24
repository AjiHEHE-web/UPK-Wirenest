<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

test('guests are redirected to login when opening the admin dashboard', function () {
    $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
});

test('regular users cannot open the admin dashboard', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertForbidden();
});

test('admins can search orders by product details', function () {
    $admin = User::factory()->create();
    $admin->forceFill(['role' => 'admin'])->save();
    $customer = User::factory()->create();
    $product = Product::factory()->create([
        'name_produk' => 'Buket Matahari Ceria',
        'deskripsi_produk' => 'Rangkaian untuk hadiah kelulusan.',
    ]);
    $order = Order::factory()->create(['id_user' => $customer->id_user]);
    OrderItem::factory()->create([
        'id_pesanan' => $order->id_pesanan,
        'id_produk' => $product->id_produk,
    ]);
    $product->delete();

    $this->actingAs($admin)
        ->get(route('admin.orders.index', ['search' => 'Matahari Ceria']))
        ->assertOk()
        ->assertSee(substr($order->id_pesanan, 0, 12));
});

test('admins can log in with their username and are sent to the admin dashboard', function () {
    $admin = User::factory()->create();
    $admin->forceFill(['role' => 'admin'])->save();

    $this->post(route('login'), [
        'email' => $admin->username,
        'password' => 'password',
    ])
        ->assertRedirect(route('admin.dashboard'));

    $this->assertAuthenticatedAs($admin);
});

test('admins can open each product order and transaction page', function () {
    Storage::fake('local');
    Storage::disk('local')->put('payments/proof.jpg', 'proof');

    $admin = User::factory()->create();
    $admin->forceFill(['role' => 'admin'])->save();
    $customer = User::factory()->create();

    DB::table('kategori')->insert([
        'id_kategori' => 'category-1',
        'name' => 'Buket',
    ]);

    DB::table('products')->insert([
        'id_produk' => 'product-1',
        'id_kategori' => 'category-1',
        'name_produk' => 'Buket Uji',
        'deskripsi_produk' => 'Bunga segar pilihan.',
        'harga_produk' => 75000,
        'stock_produk' => 5,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('pesanan')->insert([
        [
            'id_pesanan' => 'order-pending',
            'id_user' => $customer->id_user,
            'tanggal_pemesanan' => now(),
            'total_harga' => 75000,
            'status_pesanan' => 'pending',
            'created_at' => now(),
        ],
        [
            'id_pesanan' => 'order-paid',
            'id_user' => $customer->id_user,
            'tanggal_pemesanan' => now(),
            'total_harga' => 75000,
            'status_pesanan' => 'pending',
            'created_at' => now(),
        ],
    ]);

    DB::table('detail_pesanan')->insert([
        [
            'id_detail_pesanan' => 'detail-pending',
            'id_pesanan' => 'order-pending',
            'id_produk' => 'product-1',
            'harga_satuan' => 75000,
            'jumlah' => 1,
            'subtotal' => 75000,
        ],
        [
            'id_detail_pesanan' => 'detail-paid',
            'id_pesanan' => 'order-paid',
            'id_produk' => 'product-1',
            'harga_satuan' => 75000,
            'jumlah' => 1,
            'subtotal' => 75000,
        ],
    ]);

    DB::table('pembayaran')->insert([
        [
            'id_pembayaran' => 'payment-pending',
            'id_pesanan' => 'order-pending',
            'metode_pembayaran' => 'bank',
            'jumlah' => 75000,
            'status_pembayaran' => 'pending',
            'bukti_pembayaran' => null,
            'image_bukti' => 'payments/proof.jpg',
            'paid_at' => null,
            'id_admin_verifikator' => null,
            'verified_at' => null,
            'created_at' => now(),
        ],
        [
            'id_pembayaran' => 'payment-paid',
            'id_pesanan' => 'order-paid',
            'metode_pembayaran' => 'bank',
            'jumlah' => 75000,
            'status_pembayaran' => 'paid',
            'bukti_pembayaran' => 'accept',
            'image_bukti' => null,
            'paid_at' => now(),
            'id_admin_verifikator' => $admin->id_user,
            'verified_at' => now(),
            'created_at' => now(),
        ],
    ]);

    $this->actingAs($admin);

    $this->get(route('admin.products.index'))->assertOk();
    $this->get(route('admin.products.create'))->assertOk();
    $this->get(route('admin.products.show', 'product-1'))->assertOk();
    $this->get(route('admin.products.edit', 'product-1'))->assertOk();
    $this->get(route('admin.orders.index'))->assertOk();
    $this->get(route('admin.orders.show', 'order-pending'))->assertOk();
    $this->get(route('admin.transactions.pending'))->assertOk();
    $this->get(route('admin.transactions.history'))->assertOk();
    $this->get(route('admin.transactions.show', 'payment-paid'))->assertOk();
    $this->get(route('admin.transactions.proof', 'payment-pending'))->assertOk();
    $this->get(route('admin.transactions.receipt', 'payment-paid'))->assertOk();
});

test('cancelled orders disappear from the payment verification queue', function () {
    $admin = User::factory()->create();
    $admin->forceFill(['role' => 'admin'])->save();
    $customer = User::factory()->create();

    $cancelledOrder = Order::factory()->create([
        'id_user' => $customer->id_user,
        'status_pesanan' => 'cancelled',
    ]);
    Payment::factory()->create([
        'id_pembayaran' => 'cancelled-payment',
        'id_pesanan' => $cancelledOrder->id_pesanan,
        'image_bukti' => 'payments/cancelled-proof.jpg',
    ]);

    $activeOrder = Order::factory()->create(['id_user' => $customer->id_user]);
    Payment::factory()->create([
        'id_pembayaran' => 'active-payment',
        'id_pesanan' => $activeOrder->id_pesanan,
        'image_bukti' => 'payments/active-proof.jpg',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.transactions.pending'))
        ->assertOk()
        ->assertDontSee('cancelled-pa')
        ->assertSee('active-payme');
});

test('admins see dashboard statistics from stored records', function () {
    $admin = User::factory()->create();
    $admin->forceFill(['role' => 'admin'])->save();
    $customer = User::factory()->create();

    DB::table('kategori')->insert([
        'id_kategori' => 'category-1',
        'name' => 'Buket',
    ]);

    DB::table('products')->insert([
        'id_produk' => 'product-1',
        'id_kategori' => 'category-1',
        'name_produk' => 'Mawar Merah',
        'harga_produk' => 150000,
        'stock_produk' => 8,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('pesanan')->insert([
        'id_pesanan' => 'order-1',
        'id_user' => $customer->id_user,
        'tanggal_pemesanan' => now(),
        'total_harga' => 150000,
        'status_pesanan' => 'pending',
        'created_at' => now(),
    ]);

    DB::table('pembayaran')->insert([
        'id_pembayaran' => 'payment-1',
        'id_pesanan' => 'order-1',
        'metode_pembayaran' => 'bank',
        'jumlah' => 150000,
        'status_pembayaran' => 'paid',
        'bukti_pembayaran' => 'accept',
        'paid_at' => now(),
        'id_admin_verifikator' => $admin->id_user,
        'verified_at' => now(),
    ]);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Total Produk')
        ->assertSee('Mawar Merah')
        ->assertSee('Rp 150.000');
});

test('admins see current month revenue grouped by week on the dashboard', function () {
    $admin = User::factory()->create();
    $admin->forceFill(['role' => 'admin'])->save();

    Payment::factory()->create([
        'status_pembayaran' => 'paid',
        'paid_at' => now()->startOfMonth()->addDays(2),
        'jumlah' => 120000,
    ]);
    Payment::factory()->create([
        'status_pembayaran' => 'paid',
        'paid_at' => now()->startOfMonth()->addDays(9),
        'jumlah' => 50000,
    ]);
    Payment::factory()->create([
        'status_pembayaran' => 'paid',
        'paid_at' => now()->startOfMonth()->subDay(),
        'jumlah' => 90000,
    ]);
    Payment::factory()->create([
        'status_pembayaran' => 'pending',
        'paid_at' => now()->startOfMonth()->addDays(16),
        'jumlah' => 70000,
    ]);

    $response = $this->actingAs($admin)->get(route('admin.dashboard'));

    $response->assertOk()
        ->assertViewHas('revenueChart', function ($revenueChart): bool {
            expect($revenueChart->pluck('amount')->all())
                ->toBe([120000.0, 50000.0, 0.0, 0.0, 0.0]);

            return true;
        });
});

test('admins can create a product with a validated image', function () {
    Storage::fake('public');

    $admin = User::factory()->create();
    $admin->forceFill(['role' => 'admin'])->save();

    DB::table('kategori')->insert([
        'id_kategori' => 'category-1',
        'name' => 'Buket',
    ]);

    $this->actingAs($admin)
        ->post(route('admin.products.store'), [
            'name_produk' => 'Buket Musim Semi',
            'id_kategori' => 'category-1',
            'deskripsi_produk' => 'Rangkaian bunga segar.',
            'harga_produk' => 275000,
            'stock_produk' => 12,
            'image' => UploadedFile::fake()->image('buket.jpg'),
        ])
        ->assertRedirect();

    $product = DB::table('products')->where('name_produk', 'Buket Musim Semi')->first();

    expect($product)->not->toBeNull();
    expect((float) $product->harga_produk)->toBe(275000.0);
    Storage::disk('public')->assertExists($product->image);
});

test('admins receive validation errors when product data is incomplete', function () {
    $admin = User::factory()->create();
    $admin->forceFill(['role' => 'admin'])->save();

    $this->actingAs($admin)
        ->from(route('admin.products.create'))
        ->post(route('admin.products.store'), [])
        ->assertRedirect(route('admin.products.create'))
        ->assertSessionHasErrors(['name_produk', 'id_kategori', 'harga_produk', 'stock_produk', 'image']);

    $this->assertDatabaseCount('products', 0);
});

test('admins can update product details without replacing the current image', function () {
    $admin = User::factory()->create();
    $admin->forceFill(['role' => 'admin'])->save();

    DB::table('kategori')->insert([
        'id_kategori' => 'category-1',
        'name' => 'Buket',
    ]);

    DB::table('products')->insert([
        'id_produk' => 'product-1',
        'id_kategori' => 'category-1',
        'name_produk' => 'Buket Awal',
        'deskripsi_produk' => 'Deskripsi awal',
        'harga_produk' => 100000,
        'stock_produk' => 4,
        'image' => 'products/current.jpg',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs($admin)
        ->put(route('admin.products.update', 'product-1'), [
            'name_produk' => 'Buket Baru',
            'id_kategori' => 'category-1',
            'deskripsi_produk' => 'Deskripsi yang diperbarui',
            'harga_produk' => 225000,
            'stock_produk' => 10,
        ])
        ->assertRedirect(route('admin.products.show', 'product-1'));

    $this->assertDatabaseHas('products', [
        'id_produk' => 'product-1',
        'name_produk' => 'Buket Baru',
        'harga_produk' => 225000,
        'stock_produk' => 10,
        'image' => 'products/current.jpg',
    ]);
});

test('admins can delete products that are not referenced by orders or favorites', function () {
    $admin = User::factory()->create();
    $admin->forceFill(['role' => 'admin'])->save();

    DB::table('kategori')->insert([
        'id_kategori' => 'category-1',
        'name' => 'Buket',
    ]);

    DB::table('products')->insert([
        'id_produk' => 'product-1',
        'id_kategori' => 'category-1',
        'name_produk' => 'Buket Hapus',
        'harga_produk' => 100000,
        'stock_produk' => 4,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs($admin)
        ->delete(route('admin.products.destroy', 'product-1'))
        ->assertRedirect(route('admin.products.index'));

    $this->assertDatabaseMissing('products', ['id_produk' => 'product-1']);
});

test('admins cannot delete products saved as customer favorites', function () {
    $admin = User::factory()->create();
    $admin->forceFill(['role' => 'admin'])->save();
    $customer = User::factory()->create();

    DB::table('kategori')->insert([
        'id_kategori' => 'category-1',
        'name' => 'Buket',
    ]);

    DB::table('products')->insert([
        'id_produk' => 'product-1',
        'id_kategori' => 'category-1',
        'name_produk' => 'Buket Favorit',
        'harga_produk' => 100000,
        'stock_produk' => 4,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('favorites')->insert([
        'id_favorit' => 'favorite-1',
        'id_user' => $customer->id_user,
        'id_produk' => 'product-1',
        'created_at' => now(),
    ]);

    $this->actingAs($admin)
        ->from(route('admin.products.show', 'product-1'))
        ->delete(route('admin.products.destroy', 'product-1'))
        ->assertRedirect(route('admin.products.show', 'product-1'))
        ->assertSessionHas('error');

    $this->assertDatabaseHas('products', ['id_produk' => 'product-1']);
});

test('admins can delete products after all referencing orders are completed', function () {
    $admin = User::factory()->create();
    $admin->forceFill(['role' => 'admin'])->save();
    $customer = User::factory()->create();

    DB::table('kategori')->insert([
        'id_kategori' => 'category-1',
        'name' => 'Buket',
    ]);

    DB::table('products')->insert([
        'id_produk' => 'product-1',
        'id_kategori' => 'category-1',
        'name_produk' => 'Buket Selesai',
        'harga_produk' => 100000,
        'stock_produk' => 4,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('pesanan')->insert([
        'id_pesanan' => 'order-1',
        'id_user' => $customer->id_user,
        'tanggal_pemesanan' => now(),
        'total_harga' => 100000,
        'status_pesanan' => 'completed',
        'created_at' => now(),
    ]);

    DB::table('detail_pesanan')->insert([
        'id_detail_pesanan' => 'order-item-1',
        'id_pesanan' => 'order-1',
        'id_produk' => 'product-1',
        'harga_satuan' => 100000,
        'jumlah' => 1,
        'subtotal' => 100000,
    ]);

    $this->actingAs($admin)
        ->delete(route('admin.products.destroy', 'product-1'))
        ->assertRedirect(route('admin.products.index'));

    $this->assertSoftDeleted('products', ['id_produk' => 'product-1']);
    $this->assertDatabaseHas('detail_pesanan', ['id_produk' => 'product-1']);
    expect(OrderItem::query()->firstOrFail()->product->name_produk)->toBe('Buket Selesai');
});

test('admins cannot delete products while a referencing order is incomplete', function () {
    $admin = User::factory()->create();
    $admin->forceFill(['role' => 'admin'])->save();
    $customer = User::factory()->create();

    DB::table('kategori')->insert([
        'id_kategori' => 'category-1',
        'name' => 'Buket',
    ]);

    DB::table('products')->insert([
        'id_produk' => 'product-1',
        'id_kategori' => 'category-1',
        'name_produk' => 'Buket Aktif',
        'harga_produk' => 100000,
        'stock_produk' => 4,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('pesanan')->insert([
        'id_pesanan' => 'order-1',
        'id_user' => $customer->id_user,
        'tanggal_pemesanan' => now(),
        'total_harga' => 100000,
        'status_pesanan' => 'shipped',
        'created_at' => now(),
    ]);

    DB::table('detail_pesanan')->insert([
        'id_detail_pesanan' => 'order-item-1',
        'id_pesanan' => 'order-1',
        'id_produk' => 'product-1',
        'harga_satuan' => 100000,
        'jumlah' => 1,
        'subtotal' => 100000,
    ]);

    $this->actingAs($admin)
        ->from(route('admin.products.show', 'product-1'))
        ->delete(route('admin.products.destroy', 'product-1'))
        ->assertRedirect(route('admin.products.show', 'product-1'))
        ->assertSessionHas('error');

    $this->assertDatabaseHas('products', ['id_produk' => 'product-1', 'deleted_at' => null]);
});

test('admins cannot skip order processing steps', function () {
    $admin = User::factory()->create();
    $admin->forceFill(['role' => 'admin'])->save();
    $customer = User::factory()->create();

    DB::table('pesanan')->insert([
        'id_pesanan' => 'order-1',
        'id_user' => $customer->id_user,
        'tanggal_pemesanan' => now(),
        'total_harga' => 150000,
        'status_pesanan' => 'pending',
        'created_at' => now(),
    ]);

    $this->actingAs($admin)
        ->patch(route('admin.orders.update-status', 'order-1'), [
            'status_pesanan' => 'shipped',
        ])
        ->assertSessionHasErrors('status_pesanan');

    $this->assertDatabaseHas('pesanan', [
        'id_pesanan' => 'order-1',
        'status_pesanan' => 'pending',
    ]);
});

test('admins cannot cancel an order', function () {
    $admin = User::factory()->create();
    $admin->forceFill(['role' => 'admin'])->save();
    $customer = User::factory()->create();

    DB::table('pesanan')->insert([
        'id_pesanan' => 'order-1',
        'id_user' => $customer->id_user,
        'tanggal_pemesanan' => now(),
        'total_harga' => 150000,
        'status_pesanan' => 'pending',
        'created_at' => now(),
    ]);

    DB::table('pembayaran')->insert([
        'id_pembayaran' => 'payment-1',
        'id_pesanan' => 'order-1',
        'metode_pembayaran' => 'bank',
        'jumlah' => 150000,
        'status_pembayaran' => 'paid',
        'bukti_pembayaran' => 'accept',
        'paid_at' => now(),
    ]);

    $this->actingAs($admin)
        ->patch(route('admin.orders.update-status', 'order-1'), [
            'status_pesanan' => 'cancelled',
        ])
        ->assertSessionHasErrors('status_pesanan');

    $this->assertDatabaseHas('pesanan', [
        'id_pesanan' => 'order-1',
        'status_pesanan' => 'pending',
    ]);
    $this->assertDatabaseHas('pembayaran', [
        'id_pembayaran' => 'payment-1',
        'status_pembayaran' => 'paid',
    ]);
});

test('admins cannot cancel a paid processing order before it ships', function () {
    $admin = User::factory()->create();
    $admin->forceFill(['role' => 'admin'])->save();
    $customer = User::factory()->create();
    $order = Order::factory()->create([
        'id_user' => $customer->id_user,
        'status_pesanan' => 'processing',
    ]);
    Payment::factory()->create([
        'id_pesanan' => $order->id_pesanan,
        'status_pembayaran' => 'paid',
        'bukti_pembayaran' => 'accept',
        'image_bukti' => 'payments/customer-proof.jpg',
    ]);

    $this->actingAs($admin)
        ->patch(route('admin.orders.update-status', $order), [
            'status_pesanan' => 'cancelled',
        ])
        ->assertSessionHasErrors('status_pesanan');

    $this->assertDatabaseHas('pesanan', [
        'id_pesanan' => $order->id_pesanan,
        'status_pesanan' => 'processing',
    ]);
});

test('admins can accept a pending payment without advancing its order', function () {
    $admin = User::factory()->create();
    $admin->forceFill(['role' => 'admin'])->save();
    $customer = User::factory()->create();

    DB::table('pesanan')->insert([
        'id_pesanan' => 'order-1',
        'id_user' => $customer->id_user,
        'tanggal_pemesanan' => now(),
        'total_harga' => 150000,
        'status_pesanan' => 'pending',
        'created_at' => now(),
    ]);

    DB::table('pembayaran')->insert([
        'id_pembayaran' => 'payment-1',
        'id_pesanan' => 'order-1',
        'metode_pembayaran' => 'bank',
        'jumlah' => 150000,
        'status_pembayaran' => 'pending',
        'bukti_pembayaran' => null,
        'image_bukti' => 'payments/proof.jpg',
    ]);

    $this->actingAs($admin)
        ->post(route('admin.transactions.verify', 'payment-1'), [
            'decision' => 'accept',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('pembayaran', [
        'id_pembayaran' => 'payment-1',
        'status_pembayaran' => 'paid',
        'bukti_pembayaran' => 'accept',
        'id_admin_verifikator' => $admin->id_user,
    ]);

    $this->assertDatabaseHas('pesanan', [
        'id_pesanan' => 'order-1',
        'status_pesanan' => 'pending',
    ]);
});

test('admins cannot process an order until its payment proof is accepted', function () {
    $admin = User::factory()->create();
    $admin->forceFill(['role' => 'admin'])->save();
    $customer = User::factory()->create();
    $order = Order::factory()->create(['id_user' => $customer->id_user]);
    Payment::factory()->create([
        'id_pesanan' => $order->id_pesanan,
        'image_bukti' => 'payments/customer-proof.jpg',
    ]);

    $this->actingAs($admin)
        ->patch(route('admin.orders.update-status', $order), [
            'status_pesanan' => 'processing',
        ])
        ->assertSessionHasErrors('status_pesanan');

    $this->assertDatabaseHas('pesanan', [
        'id_pesanan' => $order->id_pesanan,
        'status_pesanan' => 'pending',
    ]);
});

test('admins cannot process a paid order without uploaded payment proof', function () {
    $admin = User::factory()->create();
    $admin->forceFill(['role' => 'admin'])->save();
    $customer = User::factory()->create();
    $order = Order::factory()->create(['id_user' => $customer->id_user]);
    Payment::factory()->create([
        'id_pesanan' => $order->id_pesanan,
        'status_pembayaran' => 'paid',
        'bukti_pembayaran' => 'accept',
    ]);

    $this->actingAs($admin)
        ->patch(route('admin.orders.update-status', $order), [
            'status_pesanan' => 'processing',
        ])
        ->assertSessionHasErrors('status_pesanan');

    $this->assertDatabaseHas('pesanan', [
        'id_pesanan' => $order->id_pesanan,
        'status_pesanan' => 'pending',
    ]);
});

test('admins can progress a paid order from pending to processing and shipped', function () {
    $admin = User::factory()->create();
    $admin->forceFill(['role' => 'admin'])->save();
    $customer = User::factory()->create();
    $order = Order::factory()->create(['id_user' => $customer->id_user]);
    Payment::factory()->create([
        'id_pesanan' => $order->id_pesanan,
        'status_pembayaran' => 'paid',
        'bukti_pembayaran' => 'accept',
        'image_bukti' => 'payments/customer-proof.jpg',
    ]);

    $this->actingAs($admin)
        ->patch(route('admin.orders.update-status', $order), [
            'status_pesanan' => 'processing',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('pesanan', [
        'id_pesanan' => $order->id_pesanan,
        'status_pesanan' => 'processing',
    ]);

    $this->patch(route('admin.orders.update-status', $order), [
        'status_pesanan' => 'shipped',
    ])->assertRedirect();

    $this->assertDatabaseHas('pesanan', [
        'id_pesanan' => $order->id_pesanan,
        'status_pesanan' => 'shipped',
    ]);
});

test('admins cannot mark a shipped order completed', function () {
    $admin = User::factory()->create();
    $admin->forceFill(['role' => 'admin'])->save();
    $customer = User::factory()->create();
    $order = Order::factory()->create([
        'id_user' => $customer->id_user,
        'status_pesanan' => 'shipped',
    ]);
    Payment::factory()->create([
        'id_pesanan' => $order->id_pesanan,
        'status_pembayaran' => 'paid',
        'bukti_pembayaran' => 'accept',
        'image_bukti' => 'payments/customer-proof.jpg',
    ]);

    $this->actingAs($admin)
        ->patch(route('admin.orders.update-status', $order), [
            'status_pesanan' => 'completed',
        ])
        ->assertSessionHasErrors('status_pesanan');

    $this->assertDatabaseHas('pesanan', [
        'id_pesanan' => $order->id_pesanan,
        'status_pesanan' => 'shipped',
    ]);
});

test('admins cannot accept a payment whose stored amount differs from the order total', function () {
    $admin = User::factory()->create();
    $admin->forceFill(['role' => 'admin'])->save();
    $customer = User::factory()->create();

    DB::table('pesanan')->insert([
        'id_pesanan' => 'order-1',
        'id_user' => $customer->id_user,
        'tanggal_pemesanan' => now(),
        'total_harga' => 150000,
        'status_pesanan' => 'pending',
        'created_at' => now(),
    ]);

    DB::table('pembayaran')->insert([
        'id_pembayaran' => 'payment-1',
        'id_pesanan' => 'order-1',
        'metode_pembayaran' => 'bank',
        'jumlah' => 120000,
        'status_pembayaran' => 'pending',
        'bukti_pembayaran' => null,
        'image_bukti' => 'payments/proof.jpg',
    ]);

    $this->actingAs($admin)
        ->post(route('admin.transactions.verify', 'payment-1'), [
            'decision' => 'accept',
        ])
        ->assertSessionHasErrors('decision');

    $this->assertDatabaseHas('pembayaran', [
        'id_pembayaran' => 'payment-1',
        'status_pembayaran' => 'pending',
    ]);

    $this->assertDatabaseHas('pesanan', [
        'id_pesanan' => 'order-1',
        'status_pesanan' => 'pending',
    ]);
});

test('admins can reject a pending payment without advancing its order', function () {
    $admin = User::factory()->create();
    $admin->forceFill(['role' => 'admin'])->save();
    $customer = User::factory()->create();

    DB::table('pesanan')->insert([
        'id_pesanan' => 'order-1',
        'id_user' => $customer->id_user,
        'tanggal_pemesanan' => now(),
        'total_harga' => 150000,
        'status_pesanan' => 'pending',
        'created_at' => now(),
    ]);

    DB::table('pembayaran')->insert([
        'id_pembayaran' => 'payment-1',
        'id_pesanan' => 'order-1',
        'metode_pembayaran' => 'bank',
        'jumlah' => 150000,
        'status_pembayaran' => 'pending',
        'bukti_pembayaran' => null,
        'image_bukti' => 'payments/proof.jpg',
    ]);

    $this->actingAs($admin)
        ->post(route('admin.transactions.verify', 'payment-1'), [
            'decision' => 'decline',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('pembayaran', [
        'id_pembayaran' => 'payment-1',
        'status_pembayaran' => 'failed',
        'bukti_pembayaran' => 'decline',
        'id_admin_verifikator' => $admin->id_user,
    ]);

    $this->assertDatabaseHas('pesanan', [
        'id_pesanan' => 'order-1',
        'status_pesanan' => 'pending',
    ]);
});

test('admins can view a printable receipt for paid transactions', function () {
    $admin = User::factory()->create();
    $admin->forceFill(['role' => 'admin'])->save();
    $customer = User::factory()->create();

    DB::table('kategori')->insert([
        'id_kategori' => 'category-1',
        'name' => 'Buket',
    ]);

    DB::table('products')->insert([
        'id_produk' => 'product-1',
        'id_kategori' => 'category-1',
        'name_produk' => 'Mawar Putih',
        'harga_produk' => 75000,
        'stock_produk' => 5,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('pesanan')->insert([
        'id_pesanan' => 'order-1',
        'id_user' => $customer->id_user,
        'tanggal_pemesanan' => now(),
        'total_harga' => 150000,
        'status_pesanan' => 'pending',
        'created_at' => now(),
    ]);

    DB::table('detail_pesanan')->insert([
        'id_detail_pesanan' => 'detail-1',
        'id_pesanan' => 'order-1',
        'id_produk' => 'product-1',
        'harga_satuan' => 75000,
        'jumlah' => 2,
        'subtotal' => 150000,
    ]);

    DB::table('pembayaran')->insert([
        'id_pembayaran' => 'payment-1',
        'id_pesanan' => 'order-1',
        'metode_pembayaran' => 'bank',
        'jumlah' => 150000,
        'status_pembayaran' => 'paid',
        'bukti_pembayaran' => 'accept',
        'paid_at' => now(),
    ]);

    $this->actingAs($admin)
        ->get(route('admin.transactions.receipt', 'payment-1'))
        ->assertOk()
        ->assertSee('E-Commerce Bunga')
        ->assertSee('Mawar Putih')
        ->assertSee('Cetak / Simpan PDF')
        ->assertDontSee('Navigasi Admin');
});

test('payment verification rejects an invalid decision without changing the payment', function () {
    $admin = User::factory()->create();
    $admin->forceFill(['role' => 'admin'])->save();
    $customer = User::factory()->create();

    DB::table('pesanan')->insert([
        'id_pesanan' => 'order-1',
        'id_user' => $customer->id_user,
        'tanggal_pemesanan' => now(),
        'total_harga' => 150000,
        'status_pesanan' => 'pending',
        'created_at' => now(),
    ]);

    DB::table('pembayaran')->insert([
        'id_pembayaran' => 'payment-1',
        'id_pesanan' => 'order-1',
        'metode_pembayaran' => 'bank',
        'jumlah' => 150000,
        'status_pembayaran' => 'pending',
        'bukti_pembayaran' => null,
    ]);

    $this->actingAs($admin)
        ->post(route('admin.transactions.verify', 'payment-1'), [
            'decision' => 'refund',
        ])
        ->assertSessionHasErrors('decision');

    $this->assertDatabaseHas('pembayaran', [
        'id_pembayaran' => 'payment-1',
        'status_pembayaran' => 'pending',
        'bukti_pembayaran' => null,
    ]);
});
