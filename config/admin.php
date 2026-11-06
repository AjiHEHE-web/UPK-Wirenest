<?php

return [
    'seed_admin' => [
        'name' => env('ADMIN_NAME') ?: 'Admin Toko',
        'username' => env('ADMIN_USERNAME') ?: 'admin',
        'email' => env('ADMIN_EMAIL') ?: 'admin@wirenest.local',
        'no_hp' => env('ADMIN_NO_HP', ''),
        'alamat' => env('ADMIN_ALAMAT', ''),
        'password' => env('ADMIN_PASSWORD'),
    ],
];
