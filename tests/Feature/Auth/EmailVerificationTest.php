<?php

use App\Models\User;

test('email verification endpoints are not exposed without DBML verification fields', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/verify-email')
        ->assertNotFound();
});
