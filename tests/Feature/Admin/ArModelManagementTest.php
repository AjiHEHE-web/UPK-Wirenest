<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

it('lets admins upload, replace, and remove an AR model without choosing a product', function () {
    Storage::fake('public');

    $admin = User::factory()->create();
    $admin->forceFill(['role' => 'admin'])->save();

    $this->actingAs($admin)
        ->get(route('admin.ar-models.create'))
        ->assertOk()
        ->assertSee('Nama model AR')
        ->assertSee('Berkas model AR')
        ->assertSee('GLB, GLTF, USDZ, OBJ, FBX, STL, atau DAE')
        ->assertDontSee('id_produk')
        ->assertDontSee('Produk terkait');

    $this->post(route('admin.ar-models.store'), [
        'nama_model' => 'Buket Mawar 3D',
        'image_ar' => UploadedFile::fake()->createWithContent('buket.glb', 'glTF'.str_repeat("\0", 128)),
    ])
        ->assertRedirect(route('admin.ar-models.index'))
        ->assertSessionHas('success', 'Model AR berhasil ditambahkan.');

    $modelAr = DB::table('model_ar')->where('nama_model', 'Buket Mawar 3D')->first();

    $this->assertDatabaseHas('model_ar', [
        'id_model_ar' => $modelAr->id_model_ar,
        'nama_model' => 'Buket Mawar 3D',
        'id_produk' => null,
    ]);

    expect($modelAr->id_model_ar)->toMatch('/^[0-9a-f-]{36}$/i');
    expect($modelAr->id_produk)->toBeNull();
    Storage::disk('public')->assertExists($modelAr->image_ar);

    $this->get(route('admin.ar-models.index'))
        ->assertOk()
        ->assertSee('Manajemen AR')
        ->assertSee('Buket Mawar 3D')
        ->assertSee('data-ar-qr-value="'.route('ar.show', $modelAr->id_model_ar).'"', false)
        ->assertSee('Unduh QR');

    $this->get(route('admin.ar-models.edit', $modelAr->id_model_ar))
        ->assertOk()
        ->assertSee('Simpan Perubahan')
        ->assertSee('.glb');

    $this->put(route('admin.ar-models.update', $modelAr->id_model_ar), [
        'nama_model' => 'Buket Mawar Baru',
        'image_ar' => UploadedFile::fake()->createWithContent('buket-baru.gltf', '{"asset":{"version":"2.0"}}'),
    ])
        ->assertRedirect(route('admin.ar-models.index'))
        ->assertSessionHas('success', 'Model AR berhasil diperbarui.');

    $updatedModelAr = DB::table('model_ar')->where('id_model_ar', $modelAr->id_model_ar)->first();

    expect($updatedModelAr->nama_model)->toBe('Buket Mawar Baru');
    Storage::disk('public')->assertExists($updatedModelAr->image_ar);
    Storage::disk('public')->assertMissing($modelAr->image_ar);

    $this->delete(route('admin.ar-models.destroy', $modelAr->id_model_ar))
        ->assertRedirect(route('admin.ar-models.index'))
        ->assertSessionHas('success', 'Model AR berhasil dihapus.');

    $this->assertDatabaseMissing('model_ar', ['id_model_ar' => $modelAr->id_model_ar]);
    Storage::disk('public')->assertMissing($updatedModelAr->image_ar);
});

it('rejects unsupported AR file formats without storing a model', function () {
    Storage::fake('public');

    $admin = User::factory()->create();
    $admin->forceFill(['role' => 'admin'])->save();

    $this->actingAs($admin)
        ->from(route('admin.ar-models.create'))
        ->post(route('admin.ar-models.store'), [
            'nama_model' => 'Model Tidak Didukung',
            'image_ar' => UploadedFile::fake()->createWithContent('model.zip', 'not an AR model'),
        ])
        ->assertRedirect(route('admin.ar-models.create'))
        ->assertSessionHasErrors('image_ar');

    $this->assertDatabaseCount('model_ar', 0);
    expect(Storage::disk('public')->allFiles('models'))->toBe([]);
});

it('forbids regular users from managing AR models', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.ar-models.index'))
        ->assertForbidden();
});
