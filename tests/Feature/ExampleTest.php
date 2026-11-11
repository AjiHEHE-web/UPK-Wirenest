<?php

use App\Models\User;

it('shows the public landing page and AR scanner to guests', function () {
    $this->get('/')
        ->assertOk()
        ->assertViewIs('pages.landing')
        ->assertSee('Rangkaian bunga yang')
        ->assertSee('bisa kamu lihat dalam AR')
        ->assertSee('data-ar-open-scanner', false)
        ->assertSee('id="ar-scanner-dialog"', false)
        ->assertSee(route('login'), false);
});

it('redirects signed-in customers from the landing page to their dashboard', function () {
    $customer = User::factory()->create();

    $this->actingAs($customer)
        ->get('/')
        ->assertRedirect(route('dashboard', absolute: false));
});
