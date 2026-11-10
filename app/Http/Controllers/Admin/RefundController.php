<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Refund;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class RefundController extends Controller
{
    public function index(): View
    {
        $pendingRefunds = Payment::query()
            ->with(['order.user'])
            ->paidWithApprovedProof()
            ->whereHas('order', fn ($query) => $query->where('status_pesanan', 'cancelled'))
            ->whereDoesntHave('refund')
            ->latest('paid_at')
            ->latest('id_pembayaran')
            ->paginate(10, ['*'], 'pending_page')
            ->withQueryString();

        $completedRefunds = Refund::query()
            ->with(['payment.order.user', 'processor'])
            ->latest('processed_at')
            ->paginate(10, ['*'], 'completed_page')
            ->withQueryString();

        return view('pages.admin.refunds', [
            'pendingRefunds' => $pendingRefunds,
            'completedRefunds' => $completedRefunds,
        ]);
    }

    public function store(Request $request, Payment $payment): RedirectResponse
    {
        $validated = $request->validate([
            'bukti_pengembalian' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
        ]);

        $proofPath = $validated['bukti_pengembalian']->store('refunds', 'local');

        if (! is_string($proofPath)) {
            throw new RuntimeException('Bukti pengembalian dana gagal disimpan.');
        }

        try {
            DB::transaction(function () use ($payment, $request, $proofPath): void {
                $lockedPayment = Payment::query()
                    ->whereKey($payment->getKey())
                    ->lockForUpdate()
                    ->firstOrFail();

                $order = Order::query()
                    ->whereKey($lockedPayment->id_pesanan)
                    ->lockForUpdate()
                    ->firstOrFail();

                if (
                    ! $order->payments()
                        ->paidWithApprovedProof()
                        ->whereKey($lockedPayment->getKey())
                        ->exists()
                    || $order->status_pesanan !== 'cancelled'
                    || $lockedPayment->refund()->exists()
                ) {
                    throw ValidationException::withMessages([
                        'bukti_pengembalian' => 'Pembayaran ini tidak memenuhi syarat untuk diproses sebagai pengembalian dana.',
                    ]);
                }

                $processedAt = now();

                Refund::query()->create([
                    'id_pengembalian_dana' => (string) Str::uuid(),
                    'id_pembayaran' => $lockedPayment->id_pembayaran,
                    'jumlah' => $lockedPayment->jumlah,
                    'bukti_pengembalian' => $proofPath,
                    'id_admin_pemroses' => $request->user()->id_user,
                    'processed_at' => $processedAt,
                ]);

                $lockedPayment->status_pembayaran = 'refunded';
                $lockedPayment->save();
            });
        } catch (Throwable $exception) {
            Storage::disk('local')->delete($proofPath);

            throw $exception;
        }

        return back()->with('success', 'Pengembalian dana berhasil dicatat.');
    }

    public function proof(Refund $refund): StreamedResponse
    {
        abort_unless(Storage::disk('local')->exists($refund->bukti_pengembalian), 404);

        return Storage::disk('local')->response($refund->bukti_pengembalian);
    }
}
