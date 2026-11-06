<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_produk' => (string) Str::uuid(),
            'id_kategori' => Category::factory(),
            'name_produk' => fake()->words(3, true),
            'deskripsi_produk' => fake()->paragraph(),
            'harga_produk' => fake()->randomFloat(2, 10000, 1000000),
            'stock_produk' => fake()->numberBetween(0, 100),
            'image' => null,
        ];
    }
}
