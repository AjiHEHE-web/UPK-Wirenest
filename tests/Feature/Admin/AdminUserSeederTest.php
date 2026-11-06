<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;

test('database seeder creates one configured admin account and can be rerun safely', function () {
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

    $admin = User::query()->where('email', 'admin@example.test')->first();

    expect($admin)->not->toBeNull()
        ->and($admin->name)->toBe('Admin Toko')
        ->and($admin->nama)->toBe('Admin Toko')
        ->and($admin->username)->toBe('admin-toko')
        ->and($admin->role)->toBe('admin')
        ->and(Hash::check('secure-admin-password', $admin->password))->toBeTrue()
        ->and(User::query()->where('role', 'admin')->count())->toBe(1)
        ->and(User::query()->where('role', 'user')->count())->toBe(1)
        ->and(User::query()->count())->toBe(2);
});

test('database seeder creates local accounts and prints generated passwords when credentials are not configured', function () {
    $admin = config('admin.seed_admin');
    $admin['password'] = null;
    Config::set('admin.seed_admin', $admin);

    $user = config('user.seed_user');
    $user['password'] = null;
    Config::set('user.seed_user', $user);

    $exitCode = Artisan::call('db:seed', ['--no-interaction' => true]);
    $output = Artisan::output();

    expect($exitCode)->toBe(0)
        ->and($output)->toContain('admin@wirenest.local')
        ->and($output)->toContain('user@wirenest.local')
        ->and($output)->toContain('Admin password:')
        ->and($output)->toContain('User password:');

    $this->assertDatabaseHas('users', [
        'username' => 'admin',
        'email' => 'admin@wirenest.local',
        'role' => 'admin',
    ]);
    $this->assertDatabaseHas('users', [
        'username' => 'user',
        'email' => 'user@wirenest.local',
        'role' => 'user',
    ]);

    preg_match('/Admin password: ([^\r\n]+)/', $output, $adminPassword);
    preg_match('/User password: ([^\r\n]+)/', $output, $userPassword);

    expect(isset($adminPassword[1]))->toBeTrue()
        ->and(Hash::check($adminPassword[1], User::query()->where('email', 'admin@wirenest.local')->value('password')))->toBeTrue()
        ->and(isset($userPassword[1]))->toBeTrue()
        ->and(Hash::check($userPassword[1], User::query()->where('email', 'user@wirenest.local')->value('password')))->toBeTrue();

    $secondExitCode = Artisan::call('db:seed', ['--no-interaction' => true]);
    $secondOutput = Artisan::output();

    expect($secondExitCode)->toBe(0)
        ->and($secondOutput)->not->toContain('Admin password:')
        ->and($secondOutput)->not->toContain('User password:')
        ->and(Hash::check($adminPassword[1], User::query()->where('email', 'admin@wirenest.local')->value('password')))->toBeTrue()
        ->and(Hash::check($userPassword[1], User::query()->where('email', 'user@wirenest.local')->value('password')))->toBeTrue();
});
