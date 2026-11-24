<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $monthStart = now()->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();
        $revenueByDay = Payment::query()
            ->where('status_pembayaran', 'paid')
            ->whereBetween('paid_at', [$monthStart, $monthEnd])
            ->selectRaw('DATE(paid_at) as day, SUM(jumlah) as total')
            ->groupBy('day')
            ->pluck('total', 'day');

        $weekCount = (int) ceil($monthStart->daysInMonth / 7);
        $revenueChart = collect(range(1, $weekCount))
            ->map(function (int $week) use ($monthStart, $revenueByDay): array {
                $firstDay = (($week - 1) * 7) + 1;
                $lastDay = min($week * 7, $monthStart->daysInMonth);
                $amount = collect(range($firstDay, $lastDay))
                    ->sum(fn (int $day): float => (float) ($revenueByDay[$monthStart->copy()->addDays($day - 1)->toDateString()] ?? 0));

                return [
                    'label' => "Minggu {$week}",
                    'period' => sprintf('%d–%d %s', $firstDay, $lastDay, $monthStart->translatedFormat('M')),
                    'amount' => $amount,
                ];
            });

        return view('pages.admin.dashboard', [
            'stats' => [
                'products' => Product::count(),
                'orders' => Order::count(),
                'transactions' => Payment::count(),
                'revenue' => Payment::where('status_pembayaran', 'paid')->sum('jumlah'),
            ],
            'recentOrders' => Order::query()
                ->with('user')
                ->orderByDesc('tanggal_pemesanan')
                ->orderByDesc('created_at')
                ->orderByDesc('id_pesanan')
                ->limit(6)
                ->get(),
            'pendingPayments' => Payment::query()
                ->with('order.user')
                ->where('status_pembayaran', 'pending')
                ->latest('created_at')
                ->latest('id_pembayaran')
                ->limit(5)
                ->get(),
            'stockSummary' => Product::query()
                ->with('category')
                ->orderBy('stock_produk')
                ->orderBy('name_produk')
                ->limit(5)
                ->get(),
            'revenueChart' => $revenueChart,
            'chartMaximum' => max(1, (float) $revenueChart->max('amount')),
        ]);
    }
}
