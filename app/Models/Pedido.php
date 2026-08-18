<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pedido extends Model
{
    protected $fillable = [
        'user_id',
        'mesa_id',
        'mesero_id',
        'total',
        'estado',
        'tipo_pedido',
        'observaciones'
    ];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function mesa(): BelongsTo
    {
        return $this->belongsTo(Mesa::class, 'mesa_id');
    }

    public function mesero(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mesero_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetallePedido::class, 'pedido_id');
    }

    public function factura(): HasOne
    {
        return $this->hasOne(Factura::class, 'pedido_id');
    }

    // Mutator para el estado
    public function setEstadoAttribute($value)
    {
        $map = [
            'En Espera' => 'pendiente',
            'Entregado' => 'entregado',
            'pendiente' => 'pendiente',
            'entregado' => 'entregado',
        ];
        $this->attributes['estado'] = $map[$value] ?? 'pendiente';
    }

    // Accessor para el estado
    public function getEstadoAttribute($value)
    {
        $map = [
            'pendiente' => 'En Espera',
            'en_preparacion' => 'En Espera',
            'entregado' => 'Entregado',
            'pagado' => 'Entregado',
            'cancelado' => 'Cancelado',
        ];
        return $map[$value] ?? $value;
    }

    // Mutator para tipo_pedido
    public function setTipoPedidoAttribute($value)
    {
        $this->attributes['tipo_pedido'] = strtolower($value);
    }

    // Accessor para tipo_pedido
    public function getTipoPedidoAttribute($value)
    {
        return ucfirst($value);
    }
}
