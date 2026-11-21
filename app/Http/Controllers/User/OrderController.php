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

        $search = trim($filters['search'] ?? '');
        $searchTerm = '%'.$search.'%';
        $statusSearchTerms = [
            'pending' => ['pending', 'menunggu'],
            'processing' => ['processing', 'diproses'],
            'shipped' => ['shipped', 'dikirim'],
            'completed' => ['completed', 'selesai'],
            'cancelled' => ['cancelled', 'dibatalkan'],
        ];
        $matchingStatuses = collect($statusSearchTerms)
            ->filter(fn (array $labels, string $status): bool => collect([$status, ...$labels])
                ->contains(fn (string $label): bool => str_contains(mb_strtolower($label), mb_strtolower($search))))
            ->keys()
            ->all();

        $ordersQuery = $request->user()->orders()->when($search !== '', function ($query) use ($matchingStatuses, $searchTerm): void {
            $query->where(function ($query) use ($matchingStatuses, $searchTerm): void {
                $query->where('id_pesanan', 'like', $searchTerm)
                    ->orWhere('tanggal_pemesanan', 'like', $searchTerm)
                    ->orWhere('status_pesanan', 'like', $searchTerm)
                    ->orWhereIn('status_pesanan', $matchingStatuses)
                    ->orWhere('total_harga', 'like', $searchTerm)
                    ->orWhere('alamat_pengiriman', 'like', $searchTerm)
                    ->orWhere('catatan', 'like', $searchTerm)
                    ->orWhereHas('items', function ($query) use ($searchTerm): void {
                        $query->where('jumlah', 'like', $searchTerm)
                            ->orWhere('harga_satuan', 'like', $searchTerm)
                            ->orWhere('subtotal', 'like', $searchTerm)
                            ->orWhereHas('product', function ($query) use ($searchTerm): void {
                                $query->where('id_produk', 'like', $searchTerm)
                                    ->orWhere('name_produk', 'like', $searchTerm)
                                    ->orWhere('deskripsi_produk', 'like', $searchTerm)
                                    ->orWhere('harga_produk', 'like', $searchTerm)
                                    ->orWhereHas('category', fn ($query) => $query->where('name', 'like', $searchTerm));
                            });
                    })
                    ->orWhereHas('payments', function ($query) use ($searchTerm): void {
                        $query->where('metode_pembayaran', 'like', $searchTerm)
                            ->orWhere('jumlah', 'like', $searchTerm)
                            ->orWhere('status_pembayaran', 'like', $searchTerm)
                            ->orWhere('bukti_pembayaran', 'like', $searchTerm);
                    });
            });
        });

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
            ->paginate(4)
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
