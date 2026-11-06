<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    private const STATUSES = ['pending', 'confirmed', 'processing', 'shipped', 'completed', 'cancelled'];

    private const TRANSITIONS = [
        'pending' => ['confirmed', 'cancelled'],
        'confirmed' => ['processing'],
        'processing' => ['shipped'],
        'shipped' => ['completed'],
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
                                ->orWhere('name', 'like', '%'.$search.'%')
                                ->orWhere('email', 'like', '%'.$search.'%');
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
        $nextStatuses = self::TRANSITIONS[$order->status_pesanan] ?? [];

        if ($order->status_pesanan === 'pending' && ! $order->payments()->where('status_pembayaran', 'paid')->exists()) {
            $nextStatuses = array_values(array_filter($nextStatuses, fn (string $status): bool => $status !== 'confirmed'));
        }

        return view('pages.admin.orders', [
            'page' => 'show',
            'order' => $order,
            'nextStatuses' => $nextStatuses,
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

            if ($nextStatus === 'confirmed' && ! $lockedOrder->payments()->where('status_pembayaran', 'paid')->exists()) {
                throw ValidationException::withMessages([
                    'status_pesanan' => 'Pesanan hanya dapat dikonfirmasi setelah pembayaran diterima.',
                ]);
            }

            if ($nextStatus === 'cancelled' && $lockedOrder->payments()->where('status_pembayaran', 'paid')->exists()) {
                throw ValidationException::withMessages([
                    'status_pesanan' => 'Pesanan yang sudah dibayar tidak dapat dibatalkan tanpa proses pengembalian dana.',
                ]);
            }

            $lockedOrder->status_pesanan = $nextStatus;

            if ($nextStatus === 'confirmed') {
                $lockedOrder->confirmed_at = now();
            }

            $lockedOrder->save();
        });

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }
}
