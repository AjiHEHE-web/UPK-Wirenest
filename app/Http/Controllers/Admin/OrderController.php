<?php

namespace App\Http\Controllers\Admin;

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
    private const STATUSES = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];

    private const TRANSITIONS = [
        'pending' => ['processing'],
        'processing' => ['shipped'],
        'shipped' => [],
        'completed' => [],
        'cancelled' => [],
    ];

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(self::STATUSES)],
        ]);

        $orders = Order::query()
            ->with(['user', 'payments'])
            ->when($filters['search'] ?? null, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('id_pesanan', 'like', '%'.$search.'%')
                        ->orWhereHas('user', function ($userQuery) use ($search): void {
                            $userQuery->where('nama', 'like', '%'.$search.'%')
                                ->orWhere('username', 'like', '%'.$search.'%')
                                ->orWhere('email', 'like', '%'.$search.'%');
                        })
                        ->orWhereHas('items.product', function ($productQuery) use ($search): void {
                            $productQuery->where('id_produk', 'like', '%'.$search.'%')
                                ->orWhere('name_produk', 'like', '%'.$search.'%')
                                ->orWhere('deskripsi_produk', 'like', '%'.$search.'%')
                                ->orWhere('harga_produk', 'like', '%'.$search.'%')
                                ->orWhereHas('category', fn ($categoryQuery) => $categoryQuery->where('name', 'like', '%'.$search.'%'));
                        });
                });
            })
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status_pesanan', $status))
            ->orderByDesc('tanggal_pemesanan')
            ->orderByDesc('created_at')
            ->orderByDesc('id_pesanan')
            ->paginate(10)
            ->withQueryString();

        return view('pages.admin.orders', [
            'page' => 'index',
            'orders' => $orders,
            'statuses' => self::STATUSES,
            'filters' => $filters,
        ]);
    }

    public function show(Order $order): View
    {
        $order->load(['user', 'items.product', 'payments.verifier']);
        $hasApprovedPayment = $order->payments()->paidWithApprovedProof()->exists();
        $nextStatuses = self::TRANSITIONS[$order->status_pesanan] ?? [];

        if (! $hasApprovedPayment) {
            $nextStatuses = [];
        }

        return view('pages.admin.orders', [
            'page' => 'show',
            'order' => $order,
            'nextStatuses' => $nextStatuses,
            'hasApprovedPayment' => $hasApprovedPayment,
        ]);
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status_pesanan' => ['required', Rule::in(self::STATUSES)],
        ]);

        DB::transaction(function () use ($order, $validated): void {
            $lockedOrder = Order::query()
                ->whereKey($order->getKey())
                ->lockForUpdate()
                ->firstOrFail();
            $nextStatus = $validated['status_pesanan'];
            $allowedStatuses = self::TRANSITIONS[$lockedOrder->status_pesanan] ?? [];

            if (! in_array($nextStatus, $allowedStatuses, true)) {
                throw ValidationException::withMessages([
                    'status_pesanan' => 'Perubahan status pesanan tidak diperbolehkan.',
                ]);
            }

            if (! $lockedOrder->payments()->paidWithApprovedProof()->exists()) {
                throw ValidationException::withMessages([
                    'status_pesanan' => 'Status pesanan hanya dapat diubah setelah bukti pembayaran disetujui Admin.',
                ]);
            }

            $lockedOrder->status_pesanan = $nextStatus;
            $lockedOrder->save();
        });

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }
}
