<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

#[Fillable(['name', 'nama', 'username', 'email', 'no_hp', 'alamat', 'profil_user', 'password'])]
#[Hidden(['password'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $primaryKey = 'id_user';

    public $incrementing = false;

    protected $keyType = 'string';

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->nama,
            set: fn (?string $value): array => ['nama' => $value],
        );
    }

    protected static function booted(): void
    {
        static::creating(function (User $user): void {
            $user->id_user ??= (string) Str::uuid();

            if ($user->username === null) {
                $baseUsername = Str::substr(Str::slug(Str::before($user->email, '@'), '_'), 0, 42);
                $baseUsername = $baseUsername !== '' ? $baseUsername : 'user';
                $candidate = $baseUsername;

                while (self::where('username', $candidate)->exists()) {
                    $candidate = $baseUsername.'_'.Str::lower(Str::random(5));
                }

                $user->username = $candidate;
            }
        });
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'id_user', 'id_user');
    }

    public function favorites(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'favorites', 'id_user', 'id_produk', 'id_user', 'id_produk')
            ->withPivot('id_favorit', 'created_at');
    }

    public function favoriteRecords(): HasMany
    {
        return $this->hasMany(Favorite::class, 'id_user', 'id_user');
    }

    public function verifiedPayments(): HasMany
    {
        return $this->hasMany(Payment::class, 'id_admin_verifikator', 'id_user');
    }
}
