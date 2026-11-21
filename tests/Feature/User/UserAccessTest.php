<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('registered customers must log in before opening the user home page', function () {
    $response = $this->post(route('register'), [
        'name' => 'Mawar User',
        'username' => 'mawar-user',
        'email' => 'mawar@example.test',
        'no_hp' => '081234567890',
        'alamat' => 'Jalan Mawar',
        'password' => 'secure-password',
        'password_confirmation' => 'secure-password',
    ]);

    $response->assertRedirect(route('login', absolute: false));
    $this->assertGuest();
    $this->assertDatabaseHas('users', [
        'email' => 'mawar@example.test',
        'username' => 'mawar-user',
        'nama' => 'Mawar User',
        'no_hp' => '081234567890',
        'alamat' => 'Jalan Mawar',
        'role' => 'user',
    ]);
    expect(Hash::check('secure-password', User::query()->where('email', 'mawar@example.test')->value('password')))->toBeTrue();

    $this->post(route('login'), [
        'email' => 'mawar@example.test',
        'password' => 'secure-password',
    ])->assertRedirect(route('user.home', absolute: false));

    $this->assertAuthenticated();
});

test('customers can open the user home page but admins cannot', function () {
    $customer = User::factory()->create();

    $this->actingAs($customer)
        ->get(route('user.home'))
        ->assertOk();

    $admin = User::factory()->create();
    $admin->forceFill(['role' => 'admin'])->save();

    $this->actingAs($admin)
        ->get(route('user.home'))
        ->assertForbidden();
});
