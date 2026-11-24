<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Services\User\CartService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function create(CartService $cart): View|RedirectResponse
    {
        $items = $cart->rows();

        if ($items->isEmpty()) {
            return redirect()->route('user.cart.index')->with('error', 'Keranjang Anda masih kosong.');
        }

        return view('pages.user.checkout', [
            'page' => 'create',
            'items' => $items,
            'total' => $cart->fromMinorUnits($cart->totalCents($items)),
            'walletMethods' => ['gopay' => 'GoPay', 'dana' => 'DANA', 'shopee_pay' => 'ShopeePay'],
            'bankMethods' => ['bri' => 'BRI', 'bca' => 'BCA'],
        ]);
    }

    public function store(Request $request, CartService $cart): RedirectResponse
    {
        $validated = $request->validate([
            'alamat_pengiriman' => ['required', 'string', 'max:5000'],
            'catatan' => ['nullable', 'string', 'max:2000'],
            'metode_pembayaran' => ['required', 'in:dana,gopay,bank,shopee_pay,bri,bca'],
        ]);

        $quantities = $cart->quantities();

        if ($quantities === []) {
            return redirect()->route('user.cart.index')->with('error', 'Keranjang Anda masih kosong.');
        }

        $order = DB::transaction(function () use ($request, $cart, $quantities, $validated): Order {
            $products = Product::query()
                ->whereIn('id_produk', array_keys($quantities))
                ->orderBy('id_produk')
                ->lockForUpdate()
                ->get()
                ->keyBy('id_produk');

            if ($products->count() !== count($quantities)) {
                throw ValidationException::withMessages([
                    'cart' => 'Salah satu produk di keranjang sudah tidak tersedia. Perbarui keranjang sebelum checkout.',
                ]);
            }

            $totalCents = 0;

            foreach ($quantities as $productId => $quantity) {
                $product = $products->get($productId);

                if ($quantity > $product->stock_produk) {
                    throw ValidationException::withMessages([
                        'cart' => "Stok {$product->name_produk} tidak mencukupi. Perbarui jumlah di keranjang.",
                    ]);
                }

                $unitCents = $this->toMinorUnits($product->harga_produk);
                $totalCents += $unitCents * $quantity;
            }

            $order = Order::query()->create([
                'id_pesanan' => (string) Str::uuid(),
                'id_user' => $request->user()->id_user,
                'tanggal_pemesanan' => now(),
                'created_at' => now(),
                'total_harga' => $cart->fromMinorUnits($totalCents),
                'status_pesanan' => 'pending',
                'alamat_pengiriman' => $validated['alamat_pengiriman'],
                'catatan' => $validated['catatan'] ?? null,
                'stock_reserved' => true,
            ]);

            foreach ($quantities as $productId => $quantity) {
                $product = $products->get($productId);
                $unitPrice = $product->harga_produk;
                $subtotal = $cart->fromMinorUnits($this->toMinorUnits($unitPrice) * $quantity);

                OrderItem::query()->create([
                    'id_detail_pesanan' => (string) Str::uuid(),
                    'id_pesanan' => $order->id_pesanan,
                    'id_produk' => $product->id_produk,
                    'harga_satuan' => $unitPrice,
                    'jumlah' => $quantity,
                    'subtotal' => $subtotal,
                ]);

                $product->decrement('stock_produk', $quantity);
            }

            Payment::query()->create([
                'id_pembayaran' => (string) Str::uuid(),
                'id_pesanan' => $order->id_pesanan,
                'metode_pembayaran' => $validated['metode_pembayaran'],
                'jumlah' => $order->total_harga,
                'status_pembayaran' => 'pending',
            ]);

            return $order;
        }, attempts: 3);

        $cart->clear();

        return redirect()
            ->route('user.orders.show', $order)
            ->with('success', 'Pesanan dibuat. Selesaikan pembayaran dan unggah bukti untuk diverifikasi.');
    }

    private function toMinorUnits(string $amount): int
    {
        [$whole, $fraction] = array_pad(explode('.', $amount, 2), 2, '0');

        return ((int) $whole * 100) + (int) str_pad(substr($fraction, 0, 2), 2, '0', STR_PAD_LEFT);
    }
}
