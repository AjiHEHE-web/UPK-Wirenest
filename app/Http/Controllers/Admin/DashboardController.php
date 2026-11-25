<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $filters = $request->validate([
            'month' => ['nullable', Rule::date()->format('Y-m')],
        ]);
        $currentMonth = now()->startOfMonth();
        $monthStart = isset($filters['month'])
            ? Carbon::createFromFormat('!Y-m', $filters['month'])
            : $currentMonth->copy();

        if ($monthStart->greaterThan($currentMonth)) {
            $monthStart = $currentMonth->copy();
        }

        $firstPaidAt = Payment::query()
            ->where('status_pembayaran', 'paid')
            ->whereNotNull('paid_at')
            ->min('paid_at');
        $firstRevenueMonth = $firstPaidAt
            ? Carbon::parse($firstPaidAt)->startOfMonth()
            : $currentMonth->copy();
        $monthRangeStart = $currentMonth->copy()->subMonths(11);

        if ($firstRevenueMonth->lessThan($monthRangeStart)) {
            $monthRangeStart = $firstRevenueMonth;
        }

        $monthOptions = collect();
        $optionMonth = $currentMonth->copy();

        while ($optionMonth->greaterThanOrEqualTo($monthRangeStart)) {
            $monthOptions->push([
                'value' => $optionMonth->format('Y-m'),
                'label' => $optionMonth->copy()->locale('id')->translatedFormat('F Y'),
            ]);
            $optionMonth->subMonth();
        }

        if (! $monthOptions->contains('value', $monthStart->format('Y-m'))) {
            $monthOptions->push([
                'value' => $monthStart->format('Y-m'),
                'label' => $monthStart->copy()->locale('id')->translatedFormat('F Y'),
            ]);
            $monthOptions = $monthOptions->sortByDesc('value')->values();
        }

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
            'chartMonth' => $monthStart,
            'monthOptions' => $monthOptions,
        ]);
    }
}
