<?php

namespace App\Models;

use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    /** @use HasFactory<PaymentFactory> */
    use HasFactory;

    protected $table = 'pembayaran';

    protected $primaryKey = 'id_pembayaran';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id_pembayaran',
        'id_pesanan',
        'metode_pembayaran',
        'jumlah',
        'status_pembayaran',
        'bukti_pembayaran',
        'image_bukti',
        'paid_at',
        'id_admin_verifikator',
        'verified_at',
        'id_pengembalian_dana',
        'bukti_pengembalian',
        'id_admin_pemroses',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'decimal:2',
            'paid_at' => 'datetime',
            'verified_at' => 'datetime',
            'processed_at' => 'datetime',
        ];
    }

    #[Scope]
    protected function paidWithApprovedProof(Builder $query): void
    {
        $query->where('status_pembayaran', 'paid')
            ->where('bukti_pembayaran', 'accept')
            ->whereNotNull('image_bukti')
            ->where('image_bukti', '!=', '');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'id_pesanan', 'id_pesanan');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_admin_verifikator', 'id_user');
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_admin_pemroses', 'id_user');
    }
}
