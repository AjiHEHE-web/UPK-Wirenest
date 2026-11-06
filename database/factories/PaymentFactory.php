<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_pembayaran' => (string) Str::uuid(),
            'id_pesanan' => fn (): string => Order::factory()->create()->id_pesanan,
            'metode_pembayaran' => fake()->randomElement(['dana', 'gopay', 'bank']),
            'jumlah' => fake()->randomFloat(2, 10000, 1000000),
            'status_pembayaran' => 'pending',
            'bukti_pembayaran' => null,
            'image_bukti' => null,
        ];
    }
}
