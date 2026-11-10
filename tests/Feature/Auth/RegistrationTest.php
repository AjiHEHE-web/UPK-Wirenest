<?php

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertOk()
        ->assertSeeText('Buat akun baru')
        ->assertSeeText('Nama lengkap')
        ->assertSeeText('Username')
        ->assertSeeText('Email')
        ->assertSeeText('Nomor HP')
        ->assertSeeText('Alamat')
        ->assertSeeText('Password')
        ->assertSee('name="name"', false)
        ->assertSee('name="username"', false)
        ->assertSee('name="email"', false)
        ->assertSee('name="no_hp"', false)
        ->assertSee('name="alamat"', false)
        ->assertSee('name="password"', false);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'username' => 'test-user',
        'email' => 'test@example.com',
        'no_hp' => '081234567890',
        'alamat' => 'Jalan Melati',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('user.home', absolute: false));
});

test('registration requires phone number and address', function () {
    $response = $this->from('/register')->post('/register', [
        'name' => 'Test User',
        'username' => 'test-user',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertRedirect('/register')
        ->assertSessionHasErrors(['no_hp', 'alamat']);

    $this->assertGuest();
});
