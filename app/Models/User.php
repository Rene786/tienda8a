<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    /**
     * Relación con el carrito del usuario.
     */
    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    /**
     * Relación con los pedidos del usuario.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function pdfDownloads(): HasMany
    {
        return $this->hasMany(PdfDownload::class);
    }

    /**
     * Comprobar si el usuario es administrador.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Comprobar si el usuario es cliente.
     */
    public function isClient(): bool
    {
        return $this->role === 'cliente';
    }
}
