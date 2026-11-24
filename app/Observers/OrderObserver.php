<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\Product;

class OrderObserver
{
    public function updated(Order $order): void
    {
        if (
            ! $order->wasChanged('status_pesanan')
            || ! in_array($order->status_pesanan, ['cancelled', 'rejected'], true)
            || ! $order->stock_reserved
        ) {
            return;
        }

        foreach ($order->items()->get(['id_produk', 'jumlah']) as $item) {
            Product::query()->whereKey($item->id_produk)->increment('stock_produk', $item->jumlah);
        }

        $order->forceFill(['stock_reserved' => false])->saveQuietly();
    }
}
