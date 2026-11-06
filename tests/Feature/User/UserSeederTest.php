<?php

use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;

test('database seeder creates one configured user account and can be rerun safely', function () {
    Config::set('admin.seed_admin', [
        'name' => 'Admin Toko',
        'username' => 'admin-toko',
        'email' => 'admin@example.test',
        'password' => 'secure-admin-password',
    ]);
    Config::set('user.seed_user', [
        'name' => 'Pelanggan Toko',
        'username' => 'pelanggan-toko',
        'email' => 'user@example.test',
        'password' => 'secure-user-password',
    ]);

    $this->seed();
    $this->seed();

    $user = User::query()->where('email', 'user@example.test')->first();

    expect($user)->not->toBeNull()
        ->and($user->name)->toBe('Pelanggan Toko')
        ->and($user->nama)->toBe('Pelanggan Toko')
        ->and($user->username)->toBe('pelanggan-toko')
        ->and($user->role)->toBe('user')
        ->and(Hash::check('secure-user-password', $user->password))->toBeTrue();

    expect(User::query()->where('role', 'user')->count())->toBe(1)
        ->and(User::query()->where('role', 'admin')->count())->toBe(1)
        ->and(User::query()->count())->toBe(2);
});
