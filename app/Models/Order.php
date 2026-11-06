<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    protected $table = 'pesanan';

    protected $primaryKey = 'id_pesanan';

    public $incrementing = false;

    public $timestamps = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id_pesanan',
        'id_user',
        'tanggal_pemesanan',
        'total_harga',
        'status_pesanan',
        'confirmed_at',
        'created_at',
        'alamat_pengiriman',
        'catatan',
        'stock_reserved',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pemesanan' => 'datetime',
            'confirmed_at' => 'datetime',
            'total_harga' => 'decimal:2',
            'stock_reserved' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'id_pesanan', 'id_pesanan');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'id_pesanan', 'id_pesanan');
    }
}
