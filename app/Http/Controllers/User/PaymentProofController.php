<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Services\User\UnpaidOrderExpirationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentProofController extends Controller
{
    public function store(Request $request, Order $order, UnpaidOrderExpirationService $expirationService): RedirectResponse
    {
        $validated = $request->validate([
            'payment_id' => ['required', 'string', 'exists:pembayaran,id_pembayaran'],
            'image_bukti' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'extensions:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $ownedOrder = $request->user()->orders()->whereKey($order->getKey())->firstOrFail();
        $payment = $ownedOrder->payments()->whereKey($validated['payment_id'])->firstOrFail();

        if ($expirationService->cancelIfExpired($ownedOrder->id_pesanan)) {
            throw ValidationException::withMessages([
                'image_bukti' => 'Waktu unggah bukti sudah habis. Pesanan otomatis dibatalkan dan stok dikembalikan.',
            ]);
        }

        if ($payment->status_pembayaran !== 'pending' || $ownedOrder->status_pesanan !== 'pending') {
            throw ValidationException::withMessages([
                'image_bukti' => 'Bukti hanya dapat diunggah untuk pesanan yang menunggu pembayaran.',
            ]);
        }

        if ($payment->image_bukti !== null) {
            throw ValidationException::withMessages([
                'image_bukti' => 'Bukti pembayaran sudah diunggah dan menunggu verifikasi Admin.',
            ]);
        }

        $path = $request->file('image_bukti')->store('payments', 'local');

        if ($path === false) {
            throw new RuntimeException('Bukti pembayaran gagal disimpan.');
        }

        try {
            $uploadResult = DB::transaction(function () use ($request, $order, $validated, $path, $expirationService): array {
                $lockedPayment = Payment::query()
                    ->whereKey($validated['payment_id'])
                    ->lockForUpdate()
                    ->firstOrFail();
                $lockedOrder = $request->user()
                    ->orders()
                    ->whereKey($order->getKey())
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($lockedPayment->id_pesanan !== $lockedOrder->id_pesanan) {
                    abort(404);
                }

                if (
                    $lockedOrder->status_pesanan === 'pending'
                    && $expirationService->hasExpired($lockedOrder)
                    && ! $expirationService->hasUploadedProof($lockedOrder)
                ) {
                    $expirationService->cancelExpiredOrder($lockedOrder);

                    return ['order' => $lockedOrder, 'expired' => true];
                }

                if (
                    $lockedPayment->status_pembayaran !== 'pending'
                    || $lockedOrder->status_pesanan !== 'pending'
                ) {
                    throw ValidationException::withMessages([
                        'image_bukti' => 'Bukti hanya dapat diunggah untuk pesanan yang menunggu pembayaran.',
                    ]);
                }

                if ($lockedPayment->image_bukti !== null) {
                    throw ValidationException::withMessages([
                        'image_bukti' => 'Bukti pembayaran sudah diunggah dan menunggu verifikasi Admin.',
                    ]);
                }

                $lockedPayment->update(['image_bukti' => $path]);

                return ['order' => $lockedOrder, 'expired' => false];
            }, attempts: 3);
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($path);

            throw $exception;
        }

        if ($uploadResult['expired']) {
            Storage::disk('local')->delete($path);

            throw ValidationException::withMessages([
                'image_bukti' => 'Waktu unggah bukti sudah habis. Pesanan otomatis dibatalkan dan stok dikembalikan.',
            ]);
        }

        return redirect()
            ->route('user.orders.show', $uploadResult['order'])
            ->with('success', 'Bukti pembayaran berhasil diunggah dan menunggu verifikasi Admin.');
    }

    public function show(Request $request, Order $order, Payment $payment): StreamedResponse
    {
        $ownedOrder = $request->user()->orders()->whereKey($order->getKey())->firstOrFail();
        $ownedPayment = $ownedOrder->payments()->whereKey($payment->getKey())->firstOrFail();

        abort_if($ownedPayment->image_bukti === null, 404);
        abort_unless(Storage::disk('local')->exists($ownedPayment->image_bukti), 404);

        return Storage::disk('local')->response($ownedPayment->image_bukti);
    }
}
