<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_pesanan' => (string) Str::uuid(),
            'id_user' => fn (): string => User::factory()->create()->id_user,
            'tanggal_pemesanan' => now(),
            'total_harga' => fake()->randomFloat(2, 10000, 1000000),
            'status_pesanan' => 'pending',
            'confirmed_at' => null,
            'created_at' => now(),
        ];
    }
}
