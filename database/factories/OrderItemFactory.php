<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 5);
        $unitPrice = fake()->randomFloat(2, 10000, 1000000);

        return [
            'id_detail_pesanan' => (string) Str::uuid(),
            'id_pesanan' => fn (): string => Order::factory()->create()->id_pesanan,
            'id_produk' => fn (): string => Product::factory()->create()->id_produk,
            'harga_satuan' => $unitPrice,
            'jumlah' => $quantity,
            'subtotal' => $unitPrice * $quantity,
        ];
    }
}
