<?php

use App\Models\User;

test('operators can promote an existing user account to admin from the console', function () {
    $user = User::factory()->create();

    $this->artisan('admin:promote', ['email' => $user->email])
        ->assertSuccessful();

    expect($user->fresh()->role)->toBe('admin');
});
