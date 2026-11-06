<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $chartStart = now()->subDays(6)->startOfDay();
        $revenueByDay = Payment::query()
            ->where('status_pembayaran', 'paid')
            ->whereDate('paid_at', '>=', $chartStart)
            ->selectRaw('DATE(paid_at) as day, SUM(jumlah) as total')
            ->groupBy('day')
            ->pluck('total', 'day');

        $revenueChart = collect(CarbonPeriod::create($chartStart, now()->startOfDay()))
            ->map(fn ($date): array => [
                'label' => $date->translatedFormat('D'),
                'date' => $date->toDateString(),
                'amount' => (float) ($revenueByDay[$date->toDateString()] ?? 0),
            ]);

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
