<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Refund extends Model
{
    protected $table = 'pengembalian_dana';

    protected $primaryKey = 'id_pengembalian_dana';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id_pengembalian_dana',
        'id_pembayaran',
        'jumlah',
        'bukti_pengembalian',
        'id_admin_pemroses',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'decimal:2',
            'processed_at' => 'datetime',
        ];
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'id_pembayaran', 'id_pembayaran');
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_admin_pemroses', 'id_user');
    }
}
