<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Opinion;
use App\Models\Valoracion;
use App\Models\Producto;
use App\Models\Carrito;
use App\Models\Compra;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'adress',
        'password',
        'rol',
        'numero_tarjeta',
        'nombre_titular',
        'cvv',
        'fecha_vencimiento'
    ];

    public function isAdmin()
    {
        return $this->rol === 'vendedor';
    }

    public function isCliente()
    {
        return $this->rol === 'cliente';
    }
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function productos()
    {
        return $this->hasMany(Producto::class, 'user_id');
    }

    // Relación con el modelo Opinion
    public function opiniones()
    {
        return $this->hasMany(Opinion::class, 'user_id');
    }

    // Relación con el modelo Valoracion
    public function valoraciones()
    {
        return $this->hasMany(Valoracion::class, 'user_id');
    }

    public function carritos()
    {
        return $this->hasMany(Carrito::class, 'user_id');
    }

    public function compras()
    {
        return $this->hasMany(Compra::class, 'user_id');
    }
}
