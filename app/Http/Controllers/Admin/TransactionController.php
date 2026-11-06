<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TransactionController extends Controller
{
    public function pending(): View
    {
        $payments = Payment::query()
            ->with(['order.user'])
            ->where('status_pembayaran', 'pending')
            ->latest('created_at')
            ->latest('id_pembayaran')
            ->paginate(10);

        return view('pages.admin.transactions', [
            'page' => 'pending',
            'payments' => $payments,
        ]);
    }

    public function verify(Request $request, Payment $payment): RedirectResponse
    {
        $validated = $request->validate([
            'decision' => ['required', 'in:accept,decline'],
        ]);

        DB::transaction(function () use ($request, $payment, $validated): void {
            $lockedPayment = Payment::query()
                ->whereKey($payment->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedPayment->status_pembayaran !== 'pending') {
                throw ValidationException::withMessages([
                    'decision' => 'Pembayaran ini sudah diverifikasi.',
                ]);
            }

            if ($lockedPayment->image_bukti === null) {
                throw ValidationException::withMessages([
                    'decision' => 'Bukti pembayaran belum tersedia.',
                ]);
            }

            $order = Order::query()
                ->whereKey($lockedPayment->id_pesanan)
                ->lockForUpdate()
                ->firstOrFail();

            if ($validated['decision'] === 'accept' && $lockedPayment->jumlah !== $order->total_harga) {
                throw ValidationException::withMessages([
                    'decision' => 'Jumlah pembayaran tidak sesuai dengan total pesanan.',
                ]);
            }

            if ($order->status_pesanan === 'cancelled') {
                throw ValidationException::withMessages([
                    'decision' => 'Pembayaran untuk pesanan yang dibatalkan tidak dapat diverifikasi.',
                ]);
            }

            $verifiedAt = now();
            $lockedPayment->bukti_pembayaran = $validated['decision'];
            $lockedPayment->status_pembayaran = $validated['decision'] === 'accept' ? 'paid' : 'failed';
            $lockedPayment->id_admin_verifikator = $request->user()->id_user;
            $lockedPayment->verified_at = $verifiedAt;

            if ($validated['decision'] === 'accept') {
                $lockedPayment->paid_at = $verifiedAt;

                if ($order->status_pesanan === 'pending') {
                    $order->status_pesanan = 'confirmed';
                    $order->confirmed_at = $verifiedAt;
                    $order->save();
                }
            }

            $lockedPayment->save();
        });

        return back()->with(
            'success',
            $validated['decision'] === 'accept'
                ? 'Pembayaran berhasil diterima dan pesanan dikonfirmasi.'
                : 'Pembayaran ditolak.',
        );
    }

    public function history(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:paid,failed,refunded'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $payments = Payment::query()
            ->with(['order.user', 'verifier'])
            ->whereIn('status_pembayaran', ['paid', 'failed', 'refunded'])
            ->when($filters['search'] ?? null, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('id_pembayaran', 'like', '%'.$search.'%')
                        ->orWhereHas('order', function ($orderQuery) use ($search): void {
                            $orderQuery->where('id_pesanan', 'like', '%'.$search.'%')
                                ->orWhereHas('user', function ($userQuery) use ($search): void {
                                    $userQuery->where('nama', 'like', '%'.$search.'%')
                                        ->orWhere('name', 'like', '%'.$search.'%')
                                        ->orWhere('email', 'like', '%'.$search.'%');
                                });
                        });
                });
            })
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status_pembayaran', $status))
            ->when($filters['date_from'] ?? null, fn ($query, string $date) => $query->whereRaw('DATE(COALESCE(paid_at, verified_at, created_at)) >= ?', [$date]))
            ->when($filters['date_to'] ?? null, fn ($query, string $date) => $query->whereRaw('DATE(COALESCE(paid_at, verified_at, created_at)) <= ?', [$date]))
            ->latest('verified_at')
            ->latest('created_at')
            ->latest('id_pembayaran')
            ->paginate(10)
            ->withQueryString();

        return view('pages.admin.transactions', [
            'page' => 'history',
            'payments' => $payments,
            'filters' => $filters,
        ]);
    }

    public function show(Payment $payment): View
    {
        $payment->load(['order.user', 'order.items.product', 'verifier']);

        return view('pages.admin.transactions', [
            'page' => 'show',
            'payment' => $payment,
        ]);
    }

    public function proof(Payment $payment): StreamedResponse
    {
        abort_if($payment->image_bukti === null, 404);
        abort_unless(Storage::disk('local')->exists($payment->image_bukti), 404);

        return Storage::disk('local')->response($payment->image_bukti);
    }

    public function receipt(Payment $payment): View
    {
        abort_unless($payment->status_pembayaran === 'paid', 404);

        $payment->load(['order.user', 'order.items.product']);

        return view('pages.admin.transactions', [
            'page' => 'receipt',
            'payment' => $payment,
        ]);
    }
}
