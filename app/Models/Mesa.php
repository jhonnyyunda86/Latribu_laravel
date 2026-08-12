<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mesa extends Model
{
    protected $fillable = [
        'numero',
        'capacidad',
        'estado',
        'ubicacion',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'capacidad' => 'integer',
    ];

    public function reservas(): HasMany
    {
        return $this->hasMany(Reserva::class, 'mesa_id');
    }

    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class, 'mesa_id');
    }
}
