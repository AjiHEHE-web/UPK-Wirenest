<?php

namespace App\Console\Commands;

use App\Services\User\UnpaidOrderExpirationService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:expire-unpaid-orders')]
#[Description('Cancel pending orders without payment proof after one minute')]
class ExpireUnpaidOrders extends Command
{
    public function handle(UnpaidOrderExpirationService $expirationService): int
    {
        $expiredCount = $expirationService->expirePendingOrders();

        $this->info("{$expiredCount} pesanan tanpa bukti pembayaran dibatalkan.");

        return self::SUCCESS;
    }
}
