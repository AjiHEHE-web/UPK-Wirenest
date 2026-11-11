<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArModel extends Model
{
    use HasFactory;

    public const SUPPORTED_EXTENSIONS = ['glb', 'gltf', 'usdz', 'obj', 'fbx', 'stl', 'dae'];

    public const SUPPORTED_MIME_TYPES = [
        'application/octet-stream',
        'application/json',
        'application/zip',
        'application/xml',
        'text/xml',
        'text/plain',
        'model/gltf-binary',
        'model/gltf+json',
        'model/vnd.usdz+zip',
        'model/obj',
        'model/stl',
        'application/sla',
    ];

    protected $table = 'model_ar';

    protected $primaryKey = 'id_model_ar';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'id_model_ar',
        'id_produk',
        'nama_model',
        'image_ar',
        'created_at',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'id_produk', 'id_produk');
    }
}
