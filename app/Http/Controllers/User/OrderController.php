<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['all', 'active', 'archived'])],
        ]);

        $ordersQuery = $request->user()
            ->orders()
            ->when($filters['search'] ?? null, fn ($query, string $search) => $query->where('id_pesanan', 'like', '%'.$search.'%'));

        $orderCounts = [
            'all' => (clone $ordersQuery)->count(),
            'active' => (clone $ordersQuery)->whereIn('status_pesanan', ['pending', 'processing', 'shipped'])->count(),
            'archived' => (clone $ordersQuery)->whereIn('status_pesanan', ['completed', 'cancelled'])->count(),
        ];

        $orders = (clone $ordersQuery)
            ->with(['payments', 'items.product.category'])
            ->when(($filters['status'] ?? 'all') === 'active', fn ($query) => $query->whereIn('status_pesanan', ['pending', 'processing', 'shipped']))
            ->when(($filters['status'] ?? null) === 'archived', fn ($query) => $query->whereIn('status_pesanan', ['completed', 'cancelled']))
            ->orderByDesc('tanggal_pemesanan')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('pages.user.orders', [
            'page' => 'index',
            'orders' => $orders,
            'filters' => $filters,
            'orderCounts' => $orderCounts,
        ]);
    }

    public function show(Request $request, Order $order): View
    {
        $order = $request->user()
            ->orders()
            ->with(['items.product', 'payments'])
            ->whereKey($order->getKey())
            ->firstOrFail();

        return view('pages.user.orders', [
            'page' => 'show',
            'order' => $order,
        ]);
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        DB::transaction(function () use ($request, $order): void {
            $lockedOrder = $request->user()
                ->orders()
                ->whereKey($order->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if (! in_array($lockedOrder->status_pesanan, ['pending', 'processing'], true)) {
                throw ValidationException::withMessages([
                    'order' => 'Pesanan hanya dapat dibatalkan sebelum dikirim.',
                ]);
            }

            $lockedOrder->status_pesanan = 'cancelled';
            $lockedOrder->save();
        });

        return redirect()
            ->route('user.orders.show', $order)
            ->with('success', 'Pesanan berhasil dibatalkan. Jika pembayaran telah disetujui dan bukti telah diunggah, Admin akan memproses pengembalian dana.');
    }

    public function confirmReceived(Request $request, Order $order): RedirectResponse
    {
        DB::transaction(function () use ($request, $order): void {
            $lockedOrder = $request->user()
                ->orders()
                ->whereKey($order->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if (
                $lockedOrder->status_pesanan !== 'shipped'
                || ! $lockedOrder->payments()->paidWithApprovedProof()->exists()
            ) {
                throw ValidationException::withMessages([
                    'order' => 'Pesanan hanya dapat diselesaikan setelah barang dikirim dan pembayaran disetujui.',
                ]);
            }

            $lockedOrder->status_pesanan = 'completed';
            $lockedOrder->save();
        });

        return redirect()
            ->route('user.orders.show', $order)
            ->with('success', 'Penerimaan pesanan berhasil dikonfirmasi.');
    }
}
