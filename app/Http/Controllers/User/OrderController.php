<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $orders = $request->user()
            ->orders()
            ->with('payments')
            ->when($filters['search'] ?? null, fn ($query, string $search) => $query->where('id_pesanan', 'like', '%'.$search.'%'))
            ->orderByDesc('tanggal_pemesanan')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('pages.user.orders', [
            'page' => 'index',
            'orders' => $orders,
            'filters' => $filters,
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

            if (
                $lockedOrder->status_pesanan !== 'pending'
                || $lockedOrder->payments()->where('status_pembayaran', 'paid')->exists()
            ) {
                throw ValidationException::withMessages([
                    'order' => 'Pesanan ini tidak dapat dibatalkan. Hubungi toko jika Anda memerlukan bantuan.',
                ]);
            }

            $lockedOrder->status_pesanan = 'cancelled';
            $lockedOrder->save();
        });

        return redirect()->route('user.orders.show', $order)->with('success', 'Pesanan berhasil dibatalkan.');
    }
}
