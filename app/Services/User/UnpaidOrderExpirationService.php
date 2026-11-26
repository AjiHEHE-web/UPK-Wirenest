<?php

namespace App\Services\User;

use App\Models\Order;
use Illuminate\Support\Facades\DB;

class UnpaidOrderExpirationService
{
    public function cancelIfExpired(string $orderId): bool
    {
        return DB::transaction(function () use ($orderId): bool {
            $order = Order::query()
                ->whereKey($orderId)
                ->lockForUpdate()
                ->first();

            if (
                $order === null
                || $order->status_pesanan !== 'pending'
                || ! $this->hasExpired($order)
                || $this->hasUploadedProof($order)
            ) {
                return false;
            }

            $this->cancelExpiredOrder($order);

            return true;
        }, attempts: 3);
    }

    public function expirePendingOrders(): int
    {
        $expiredCount = 0;
        $cutoff = now()->subHours(Order::PAYMENT_PROOF_DEADLINE_HOURS);

        Order::query()
            ->where('status_pesanan', 'pending')
            ->where(function ($query) use ($cutoff): void {
                $query->where('created_at', '<', $cutoff)
                    ->orWhere(function ($query) use ($cutoff): void {
                        $query->whereNull('created_at')
                            ->where('tanggal_pemesanan', '<', $cutoff);
                    });
            })
            ->whereDoesntHave('payments', static function ($query): void {
                $query->whereNotNull('image_bukti')->where('image_bukti', '!=', '');
            })
            ->pluck('id_pesanan')
            ->each(function (string $orderId) use (&$expiredCount): void {
                if ($this->cancelIfExpired($orderId)) {
                    $expiredCount++;
                }
            });

        return $expiredCount;
    }

    public function hasExpired(Order $order): bool
    {
        $createdAt = $order->created_at ?? $order->tanggal_pemesanan;

        return $createdAt !== null
            && $createdAt->isBefore(now()->subHours(Order::PAYMENT_PROOF_DEADLINE_HOURS));
    }

    public function hasUploadedProof(Order $order): bool
    {
        return $order->payments()
            ->whereNotNull('image_bukti')
            ->where('image_bukti', '!=', '')
            ->exists();
    }

    public function cancelExpiredOrder(Order $order): void
    {
        $order->status_pesanan = 'cancelled';
        $order->save();

        $order->payments()
            ->where('status_pembayaran', 'pending')
            ->update(['status_pembayaran' => 'failed']);
    }
}
