<?php

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

it('does not open a separate scanner page', function () {
    $this->get('/ar/scan')->assertNotFound();
});

it('opens an AR model from its QR without signing in', function () {
    $modelId = '20ba8310-adee-4e4b-ad23-1a1fdb1ba1cd';

    DB::table('model_ar')->insert([
        'id_model_ar' => $modelId,
        'id_produk' => null,
        'nama_model' => 'Model Buket',
        'image_ar' => 'models/buket.glb',
        'created_at' => now(),
    ]);

    config(['filesystems.disks.public.url' => 'http://localhost:8000/storage']);

    $this->get(route('ar.show', $modelId))
        ->assertOk()
        ->assertSee('Model Buket')
        ->assertSee('data-model-url="/storage/models/buket.glb"', false)
        ->assertDontSee('http://localhost:8000/storage/models/buket.glb')
        ->assertSee('data-ar-viewer', false)
        ->assertSee('Lihat dalam AR');
});

it('offers the public AR scanner from order history', function () {
    $customer = User::factory()->create();
    Order::factory()->create(['id_user' => $customer->id_user]);

    $this->actingAs($customer)
        ->get(route('user.orders.index'))
        ->assertOk()
        ->assertSee('data-ar-open-scanner', false)
        ->assertSee('id="ar-scanner-dialog"', false)
        ->assertDontSee('href="/ar/scan"', false)
        ->assertSee('Scan QR AR');
});
