<?php

namespace App\Services\User;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class CartService
{
    /**
     * @return array<string, int>
     */
    public function quantities(): array
    {
        $quantities = session('cart', []);

        if (! is_array($quantities)) {
            return [];
        }

        return collect($quantities)
            ->filter(fn ($quantity, $productId): bool => is_string($productId)
                && is_numeric($quantity)
                && (int) $quantity > 0)
            ->map(fn ($quantity): int => (int) $quantity)
            ->all();
    }

    /**
     * @return Collection<int, array{product: Product, quantity: int, unit_price: string, subtotal: string, subtotal_cents: int}>
     */
    public function rows(): Collection
    {
        $quantities = $this->quantities();
        $products = Product::query()
            ->with('category')
            ->whereIn('id_produk', array_keys($quantities))
            ->get()
            ->keyBy('id_produk');

        $rows = collect($quantities)
            ->map(function (int $quantity, string $productId) use ($products): ?array {
                $product = $products->get($productId);

                if ($product === null) {
                    return null;
                }

                $subtotalCents = $this->toMinorUnits($product->harga_produk) * $quantity;

                return [
                    'product' => $product,
                    'quantity' => $quantity,
                    'unit_price' => $product->harga_produk,
                    'subtotal' => $this->fromMinorUnits($subtotalCents),
                    'subtotal_cents' => $subtotalCents,
                ];
            })
            ->filter()
            ->values();

        $validQuantities = $rows->mapWithKeys(fn (array $row): array => [
            $row['product']->id_produk => $row['quantity'],
        ])->all();

        if ($validQuantities !== $quantities) {
            session()->put('cart', $validQuantities);
        }

        return $rows;
    }

    public function add(Product $product, int $quantity): void
    {
        $quantities = $this->quantities();
        $updatedQuantity = ($quantities[$product->id_produk] ?? 0) + $quantity;

        if ($updatedQuantity > $product->stock_produk) {
            throw ValidationException::withMessages([
                'quantity' => 'Jumlah yang dipilih melebihi stok yang tersedia.',
            ]);
        }

        $quantities[$product->id_produk] = $updatedQuantity;
        session()->put('cart', $quantities);
    }

    public function update(Product $product, int $quantity): void
    {
        if ($quantity > $product->stock_produk) {
            throw ValidationException::withMessages([
                'quantity' => 'Jumlah yang dipilih melebihi stok yang tersedia.',
            ]);
        }

        $quantities = $this->quantities();
        $quantities[$product->id_produk] = $quantity;
        session()->put('cart', $quantities);
    }

    public function remove(Product $product): void
    {
        $quantities = $this->quantities();
        unset($quantities[$product->id_produk]);
        session()->put('cart', $quantities);
    }

    public function clear(): void
    {
        session()->forget('cart');
    }

    public function totalCents(Collection $rows): int
    {
        return $rows->sum('subtotal_cents');
    }

    public function fromMinorUnits(int $amount): string
    {
        return intdiv($amount, 100).'.'.str_pad((string) ($amount % 100), 2, '0', STR_PAD_LEFT);
    }

    private function toMinorUnits(string $amount): int
    {
        [$whole, $fraction] = array_pad(explode('.', $amount, 2), 2, '0');

        return ((int) $whole * 100) + (int) str_pad(substr($fraction, 0, 2), 2, '0');
    }
}
