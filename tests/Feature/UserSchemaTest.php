<?php

use App\Models\User;
use Illuminate\Support\Facades\Schema;

test('users table follows the DBML schema', function () {
    $columns = collect(Schema::getColumns('users'))->keyBy('name');
    $indexes = collect(Schema::getIndexes('users'));

    expect($columns->keys()->all())->toBe([
        'id_user',
        'nama',
        'username',
        'email',
        'password',
        'no_hp',
        'alamat',
        'role',
        'profil_user',
        'created_at',
        'updated_at',
    ]);

    expect($indexes->firstWhere('primary', true)['columns'])->toBe(['id_user'])
        ->and($columns['id_user']['type'])->toBe('varchar')
        ->and($columns['nama']['nullable'])->toBeFalse()
        ->and($columns['username']['nullable'])->toBeFalse()
        ->and($columns['email']['nullable'])->toBeFalse()
        ->and($columns['password']['nullable'])->toBeFalse()
        ->and($columns['no_hp']['nullable'])->toBeFalse()
        ->and($columns['alamat']['nullable'])->toBeFalse()
        ->and($columns['role']['nullable'])->toBeFalse()
        ->and($columns['profil_user']['nullable'])->toBeTrue();

});

test('user records use their UUID primary key and require contact details', function () {
    $user = User::factory()->create();

    expect($user->getKeyName())->toBe('id_user')
        ->and($user->getKey())->toBe($user->id_user)
        ->and($user->getKey())->toBeString()
        ->and($user->nama)->not->toBeEmpty()
        ->and($user->no_hp)->not->toBeEmpty()
        ->and($user->alamat)->not->toBeEmpty();
});
