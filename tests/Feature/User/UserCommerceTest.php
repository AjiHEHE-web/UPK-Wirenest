<?php

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('customers can search available products by name and category', function () {
    $customer = User::factory()->create();
    $category = Category::factory()->create(['name' => 'Mawar']);
    $available = Product::factory()->for($category, 'category')->create([
        'name_produk' => 'Mawar Merah',
        'stock_produk' => 3,
    ]);
    Product::factory()->for($category, 'category')->create([
        'name_produk' => 'Mawar Habis',
        'stock_produk' => 0,
    ]);

    $this->actingAs($customer)
        ->get(route('user.catalog.index', ['search' => 'Mawar', 'category' => $category->id_kategori]))
        ->assertOk()
        ->assertSee($available->name_produk)
        ->assertDontSee('Mawar Habis');
});

test('customers can open the catalog detail favorites cart checkout order history and profile pages', function () {
    $customer = User::factory()->create();
    $product = Product::factory()->create(['stock_produk' => 3]);
    $order = Order::factory()->create(['id_user' => $customer->id_user]);
    OrderItem::factory()->create([
        'id_pesanan' => $order->id_pesanan,
        'id_produk' => $product->id_produk,
    ]);

    $this->actingAs($customer);

    $this->get(route('user.catalog.show', $product))->assertOk();
    $this->post(route('user.favorites.store', $product))->assertRedirect(route('user.favorites.index'));
    $this->get(route('user.favorites.index'))->assertOk();
    $this->withSession(['cart' => [$product->id_produk => 1]])
        ->get(route('user.checkout.create'))
        ->assertOk();
    $this->get(route('user.orders.index'))->assertOk();
    $this->get(route('user.orders.show', $order))->assertOk();
    $this->get(route('user.profile.show'))->assertOk();
});

test('related user routes render a single blade file per page group', function () {
    $customer = User::factory()->create();
    $product = Product::factory()->create();
    $order = Order::factory()->create(['id_user' => $customer->id_user]);
    OrderItem::factory()->create([
        'id_pesanan' => $order->id_pesanan,
        'id_produk' => $product->id_produk,
    ]);

    $this->actingAs($customer);

    $this->get(route('user.catalog.index'))
        ->assertViewIs('pages.user.catalog')
        ->assertViewHas('page', 'index');
    $this->get(route('user.catalog.show', $product))
        ->assertViewIs('pages.user.catalog')
        ->assertViewHas('page', 'show');
    $this->get(route('user.favorites.index'))
        ->assertViewIs('pages.user.favorites');
    $this->get(route('user.cart.index'))
        ->assertViewIs('pages.user.cart');
    $this->withSession(['cart' => [$product->id_produk => 1]])
        ->get(route('user.checkout.create'))
        ->assertViewIs('pages.user.checkout');
    $this->get(route('user.orders.index'))
        ->assertViewIs('pages.user.orders')
        ->assertViewHas('page', 'index');
    $this->get(route('user.orders.show', $order))
        ->assertViewIs('pages.user.orders')
        ->assertViewHas('page', 'show');
});

test('customers can add a product to favorites without creating duplicates', function () {
    $customer = User::factory()->create();
    $product = Product::factory()->create();

    $this->actingAs($customer)
        ->post(route('user.favorites.store', $product))
        ->assertRedirect(route('user.favorites.index'));

    $this->post(route('user.favorites.store', $product))
        ->assertRedirect(route('user.favorites.index'));

    $this->assertDatabaseCount('favorites', 1);
    $this->assertDatabaseHas('favorites', [
        'id_user' => $customer->id_user,
        'id_produk' => $product->id_produk,
    ]);

    $this->delete(route('user.favorites.destroy', $product))
        ->assertRedirect(route('user.favorites.index'));

    $this->assertDatabaseCount('favorites', 0);
});

test('the cart displays current database prices and recalculates quantity totals', function () {
    $customer = User::factory()->create();
    $product = Product::factory()->create([
        'name_produk' => 'Buket Uji',
        'harga_produk' => '12500.75',
        'stock_produk' => 4,
    ]);

    $this->actingAs($customer)
        ->post(route('user.cart.store', $product), ['quantity' => 2])
        ->assertRedirect(route('user.cart.index'));

    $this->get(route('user.cart.index'))
        ->assertOk()
        ->assertSee('Buket Uji')
        ->assertSee('Rp 25.001,50');
});

test('checkout creates a pending order at database prices and reserves stock atomically', function () {
    $customer = User::factory()->create();
    $product = Product::factory()->create([
        'harga_produk' => '12500.75',
        'stock_produk' => 4,
    ]);

    $this->actingAs($customer)
        ->withSession(['cart' => [$product->id_produk => 2]])
        ->post(route('user.checkout.store'), [
            'alamat_pengiriman' => 'Jalan Melati 10',
            'catatan' => 'Tolong bungkus dengan rapi',
            'metode_pembayaran' => 'bank',
            'total_harga' => 1,
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    $order = Order::query()->sole();
    $item = OrderItem::query()->sole();
    $payment = Payment::query()->sole();

    expect($order->id_user)->toBe($customer->id_user)
        ->and($order->total_harga)->toBe('25001.50')
        ->and($order->status_pesanan)->toBe('pending')
        ->and($item->harga_satuan)->toBe('12500.75')
        ->and($item->jumlah)->toBe(2)
        ->and($item->subtotal)->toBe('25001.50')
        ->and($payment->jumlah)->toBe('25001.50')
        ->and($payment->metode_pembayaran)->toBe('bank')
        ->and($payment->status_pembayaran)->toBe('pending')
        ->and($product->fresh()->stock_produk)->toBe(2);
});

test('checkout rejects a quantity that exceeds stock without creating an order', function () {
    $customer = User::factory()->create();
    $product = Product::factory()->create(['stock_produk' => 1]);

    $this->actingAs($customer)
        ->withSession(['cart' => [$product->id_produk => 2]])
        ->post(route('user.checkout.store'), [
            'alamat_pengiriman' => 'Jalan Melati 10',
            'metode_pembayaran' => 'dana',
        ])
        ->assertSessionHasErrors('cart')
        ->assertRedirect();

    $this->assertDatabaseCount('pesanan', 0);
    $this->assertDatabaseCount('detail_pesanan', 0);
    $this->assertDatabaseCount('pembayaran', 0);
    expect($product->fresh()->stock_produk)->toBe(1);
});

test('customers can upload private payment proof for their own pending order', function () {
    Storage::fake('local');
    $customer = User::factory()->create();
    $order = Order::factory()->create(['id_user' => $customer->id_user]);
    $payment = Payment::factory()->create(['id_pesanan' => $order->id_pesanan]);

    $this->actingAs($customer)
        ->post(route('user.orders.payment-proof', $order), [
            'payment_id' => $payment->id_pembayaran,
            'image_bukti' => UploadedFile::fake()->image('transfer.jpg'),
        ])
        ->assertRedirect(route('user.orders.show', $order));

    expect($payment->fresh()->image_bukti)->not->toBeNull()
        ->and($payment->fresh()->status_pembayaran)->toBe('pending');
    Storage::disk('local')->assertExists($payment->fresh()->image_bukti);

    $this->get(route('user.orders.show', $order))
        ->assertOk()
        ->assertSee('Lihat bukti pembayaran')
        ->assertDontSee('Unggah bukti transfer');
});

test('customers cannot replace an uploaded payment proof', function () {
    Storage::fake('local');
    $customer = User::factory()->create();
    $order = Order::factory()->create(['id_user' => $customer->id_user]);
    $payment = Payment::factory()->create([
        'id_pesanan' => $order->id_pesanan,
        'image_bukti' => 'payments/existing-proof.jpg',
    ]);
    Storage::disk('local')->put('payments/existing-proof.jpg', 'existing proof');

    $this->actingAs($customer)
        ->post(route('user.orders.payment-proof', $order), [
            'payment_id' => $payment->id_pembayaran,
            'image_bukti' => UploadedFile::fake()->image('replacement.jpg'),
        ])
        ->assertSessionHasErrors('image_bukti');

    expect($payment->fresh()->image_bukti)->toBe('payments/existing-proof.jpg');
    Storage::disk('local')->assertExists('payments/existing-proof.jpg');
});

test('customers cannot view another customers order', function () {
    $customer = User::factory()->create();
    $otherCustomer = User::factory()->create();
    $order = Order::factory()->create(['id_user' => $otherCustomer->id_user]);

    $this->actingAs($customer)
        ->get(route('user.orders.show', $order))
        ->assertNotFound();
});

test('customers can cancel pending orders and release reserved stock', function () {
    $customer = User::factory()->create();
    $product = Product::factory()->create(['stock_produk' => 3]);
    $order = Order::factory()->create([
        'id_user' => $customer->id_user,
        'stock_reserved' => true,
    ]);
    OrderItem::factory()->create([
        'id_pesanan' => $order->id_pesanan,
        'id_produk' => $product->id_produk,
        'jumlah' => 2,
    ]);

    $this->actingAs($customer)
        ->patch(route('user.orders.cancel', $order))
        ->assertRedirect(route('user.orders.show', $order));

    expect($order->fresh()->status_pesanan)->toBe('cancelled')
        ->and($order->fresh()->stock_reserved)->toBeFalse()
        ->and($product->fresh()->stock_produk)->toBe(5);
});

test('customers can cancel an unshipped paid order in processing and release reserved stock', function () {
    $customer = User::factory()->create();
    $product = Product::factory()->create(['stock_produk' => 3]);
    $order = Order::factory()->create([
        'id_user' => $customer->id_user,
        'status_pesanan' => 'processing',
        'stock_reserved' => true,
    ]);
    OrderItem::factory()->create([
        'id_pesanan' => $order->id_pesanan,
        'id_produk' => $product->id_produk,
        'jumlah' => 2,
    ]);
    Payment::factory()->create([
        'id_pesanan' => $order->id_pesanan,
        'status_pembayaran' => 'paid',
        'bukti_pembayaran' => 'accept',
        'image_bukti' => 'payments/customer-proof.jpg',
    ]);

    $this->actingAs($customer)
        ->patch(route('user.orders.cancel', $order))
        ->assertRedirect(route('user.orders.show', $order));

    expect($order->fresh()->status_pesanan)->toBe('cancelled')
        ->and($order->fresh()->stock_reserved)->toBeFalse()
        ->and($product->fresh()->stock_produk)->toBe(5);
});

test('customers cannot cancel an order after it has shipped', function () {
    $customer = User::factory()->create();
    $order = Order::factory()->create([
        'id_user' => $customer->id_user,
        'status_pesanan' => 'shipped',
    ]);

    $this->actingAs($customer)
        ->patch(route('user.orders.cancel', $order))
        ->assertSessionHasErrors('order');

    expect($order->fresh()->status_pesanan)->toBe('shipped');
});

test('customers can mark a shipped paid order as received', function () {
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

    $this->actingAs($customer)
        ->patch(route('user.orders.confirm-received', $order))
        ->assertRedirect(route('user.orders.show', $order));

    expect($order->fresh()->status_pesanan)->toBe('completed');
});

test('customers cannot mark an unshipped order as received', function () {
    $customer = User::factory()->create();
    $order = Order::factory()->create(['id_user' => $customer->id_user]);

    $this->actingAs($customer)
        ->patch(route('user.orders.confirm-received', $order))
        ->assertSessionHasErrors('order');

    expect($order->fresh()->status_pesanan)->toBe('pending');
});

test('customers cannot mark a shipped order received before payment approval', function () {
    $customer = User::factory()->create();
    $order = Order::factory()->create([
        'id_user' => $customer->id_user,
        'status_pesanan' => 'shipped',
    ]);
    Payment::factory()->create([
        'id_pesanan' => $order->id_pesanan,
        'image_bukti' => 'payments/customer-proof.jpg',
    ]);

    $this->actingAs($customer)
        ->patch(route('user.orders.confirm-received', $order))
        ->assertSessionHasErrors('order');

    expect($order->fresh()->status_pesanan)->toBe('shipped');
});

test('customers can update their profile and password', function () {
    $customer = User::factory()->create();

    $this->actingAs($customer)
        ->put(route('user.profile.update'), [
            'name' => 'Nama Baru',
            'username' => 'nama-baru',
            'email' => 'nama-baru@example.test',
            'no_hp' => '081234567890',
            'alamat' => 'Jalan Anggrek',
        ])
        ->assertRedirect(route('user.profile.show'));

    expect($customer->fresh()->name)->toBe('Nama Baru')
        ->and($customer->fresh()->username)->toBe('nama-baru')
        ->and($customer->fresh()->email)->toBe('nama-baru@example.test');

    $this->put(route('user.profile.password'), [
        'current_password' => 'password',
        'password' => 'another-secure-password',
        'password_confirmation' => 'another-secure-password',
    ])->assertRedirect(route('user.profile.show'));

    $this->assertCredentials([
        'email' => 'nama-baru@example.test',
        'password' => 'another-secure-password',
    ]);
});
