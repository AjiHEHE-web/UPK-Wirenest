<?php

use App\Http\Controllers\Admin\ArModelController as AdminArModelController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\RefundController as AdminRefundController;
use App\Http\Controllers\Admin\TransactionController as AdminTransactionController;
use App\Http\Controllers\ArExperienceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\CartController as UserCartController;
use App\Http\Controllers\User\CatalogController as UserCatalogController;
use App\Http\Controllers\User\CheckoutController as UserCheckoutController;
use App\Http\Controllers\User\FavoriteController as UserFavoriteController;
use App\Http\Controllers\User\HomeController as UserHomeController;
use App\Http\Controllers\User\OrderController as UserOrderController;
use App\Http\Controllers\User\PaymentProofController as UserPaymentProofController;
use App\Http\Controllers\User\ProfileController as UserProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function (UserHomeController $homeController) {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return $homeController(request())->with('landingToast', 'Selamat datang di WireNest. Jelajahi buket pilihan kami.');
})->name('landing');

Route::get('/ar/{arModel}', [ArExperienceController::class, 'show'])
    ->whereUuid('arModel')
    ->name('ar.show');

Route::get('/user/katalog', [UserCatalogController::class, 'index'])->name('user.catalog.index');
Route::get('/user/katalog/{product}', [UserCatalogController::class, 'show'])->name('user.catalog.show');

Route::get('/dashboard', function () {
    return redirect()->route(auth()->user()->role === 'admin' ? 'admin.dashboard' : 'user.home');
})->middleware('auth')->name('dashboard');

Route::middleware(['auth', 'user'])
    ->prefix('user')
    ->name('user.')
    ->group(function (): void {
        Route::get('/', UserHomeController::class)->name('home');
        Route::get('/favorit', [UserFavoriteController::class, 'index'])->name('favorites.index');
        Route::post('/favorit/{product}', [UserFavoriteController::class, 'store'])->name('favorites.store');
        Route::delete('/favorit/{product}', [UserFavoriteController::class, 'destroy'])->name('favorites.destroy');

        Route::get('/keranjang', [UserCartController::class, 'index'])->name('cart.index');
        Route::post('/keranjang/{product}', [UserCartController::class, 'store'])->name('cart.store');
        Route::patch('/keranjang/{product}', [UserCartController::class, 'update'])->name('cart.update');
        Route::delete('/keranjang/{product}', [UserCartController::class, 'destroy'])->name('cart.destroy');
        Route::delete('/keranjang', [UserCartController::class, 'clear'])->name('cart.clear');

        Route::get('/checkout', [UserCheckoutController::class, 'create'])->name('checkout.create');
        Route::post('/checkout', [UserCheckoutController::class, 'store'])->name('checkout.store');

        Route::get('/history', [UserOrderController::class, 'index'])->name('orders.index');
        Route::get('/history/{order}', [UserOrderController::class, 'show'])->name('orders.show');
        Route::patch('/history/{order}/cancel', [UserOrderController::class, 'cancel'])->name('orders.cancel');
        Route::patch('/history/{order}/received', [UserOrderController::class, 'confirmReceived'])->name('orders.confirm-received');
        Route::post('/history/{order}/payment-proof', [UserPaymentProofController::class, 'store'])->name('orders.payment-proof');
        Route::get('/history/{order}/payment-proof/{payment}', [UserPaymentProofController::class, 'show'])->name('orders.payment-proof.show');

        Route::get('/profil', [UserProfileController::class, 'show'])->name('profile.show');
        Route::put('/profil', [UserProfileController::class, 'update'])->name('profile.update');
        Route::put('/profil/password', [UserProfileController::class, 'updatePassword'])->name('profile.password');
    });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function (): void {
        Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
        Route::resource('ar-models', AdminArModelController::class)->except(['show']);
        Route::resource('products', AdminProductController::class);
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');
        Route::get('/refunds', [AdminRefundController::class, 'index'])->name('refunds.index');
        Route::post('/refunds/{payment}', [AdminRefundController::class, 'store'])->name('refunds.store');
        Route::get('/refunds/{payment}/proof', [AdminRefundController::class, 'proof'])->name('refunds.proof');
        Route::get('/transactions', [AdminTransactionController::class, 'pending'])->name('transactions.pending');
        Route::post('/transactions/{payment}/verify', [AdminTransactionController::class, 'verify'])->name('transactions.verify');
        Route::get('/transactions/history', [AdminTransactionController::class, 'history'])->name('transactions.history');
        Route::get('/transactions/{payment}/proof', [AdminTransactionController::class, 'proof'])->name('transactions.proof');
        Route::get('/transactions/{payment}', [AdminTransactionController::class, 'show'])->name('transactions.show');
        Route::get('/transactions/{payment}/receipt', [AdminTransactionController::class, 'receipt'])->name('transactions.receipt');
    });

require __DIR__.'/auth.php';
