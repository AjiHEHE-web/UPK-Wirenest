<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    protected $table = 'products';

    protected $primaryKey = 'id_produk';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id_produk',
        'id_kategori',
        'name_produk',
        'deskripsi_produk',
        'harga_produk',
        'stock_produk',
        'image',
    ];

    protected function casts(): array
    {
        return [
            'harga_produk' => 'decimal:2',
            'stock_produk' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'id_kategori', 'id_kategori');
    }

    public function arModel(): HasOne
    {
        return $this->hasOne(ArModel::class, 'id_produk', 'id_produk');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'id_produk', 'id_produk');
    }

    public function favorites(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites', 'id_produk', 'id_user', 'id_produk', 'id_user')
            ->withPivot('id_favorit', 'created_at');
    }

    public function favoriteRecords(): HasMany
    {
        return $this->hasMany(Favorite::class, 'id_produk', 'id_produk');
    }
}
