<?php

return [
    'seed_user' => [
        'name' => env('USER_NAME') ?: 'Pelanggan Toko',
        'username' => env('USER_USERNAME') ?: 'user',
        'email' => env('USER_EMAIL') ?: 'user@wirenest.local',
        'no_hp' => env('USER_NO_HP', ''),
        'alamat' => env('USER_ALAMAT', ''),
        'password' => env('USER_PASSWORD'),
    ],
];
