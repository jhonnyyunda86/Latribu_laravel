<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    protected $fillable = [
        'categoria_id',
        'nombre',
        'descripcion',
        'precio',
        'imagen',
        'disponible',
        'stock'
    ];

    protected $casts = [
        'disponible' => 'boolean',
        'precio' => 'decimal:2',
        'stock' => 'integer'
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function inventarios(): HasMany
    {
        return $this->hasMany(Inventario::class, 'producto_id');
    }

    public function detallesPedido(): HasMany
    {
        return $this->hasMany(DetallePedido::class, 'producto_id');
    }

    public function detallesFactura(): HasMany
    {
        return $this->hasMany(DetalleFactura::class, 'producto_id');
    }
}
