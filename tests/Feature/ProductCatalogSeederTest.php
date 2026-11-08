<?php

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Config;

test('database seeder creates categories and related products idempotently', function () {
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
    $firstCategoryCount = Category::query()->count();
    $firstProductCount = Product::query()->count();

    $this->seed();

    expect($firstCategoryCount)->toBe(4)
        ->and($firstProductCount)->toBe(8)
        ->and(Category::query()->count())->toBe($firstCategoryCount)
        ->and(Product::query()->count())->toBe($firstProductCount)
        ->and(Product::query()->whereDoesntHave('category')->count())->toBe(0);

    expect(Product::query()->where('name_produk', 'Mawar Merah Premium')->firstOrFail()->category->name)
        ->toBe('Bunga Potong');
});
