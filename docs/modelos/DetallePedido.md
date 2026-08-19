# Documentación del Modelo: DetallePedido

**Ruta física:** `app/Models/DetallePedido.php`

El modelo `DetallePedido` representa el desglose de productos individuales incluidos dentro de una orden de consumo o pedido.

---

## 1. Código Fuente Explicado

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetallePedido extends Model
{
    // Nombre de la tabla física
    protected $table = 'detalle_pedidos';

    // Atributos asignables masivamente
    protected $fillable = [
        'pedido_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'subtotal'
    ];

    // Conversión de tipos automática
    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    // Relación con el Pedido padre
    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    // Relación con el Producto asociado
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
```

---

## 2. Explicación de la Lógica del Código

### 1. `$table` (Línea 10)
Indica explícitamente a Eloquent que debe mapear este modelo con la tabla física `detalle_pedidos` de la base de datos (por defecto, Laravel buscaría la traducción en plural simple `detalle_pedidos`).

### 2. Relaciones Eloquent
* **`pedido()`**: Relación `BelongsTo` con `Pedido`. Vincula el artículo con el pedido general del cliente.
* **`producto()`**: Relación `BelongsTo` con `Producto`. Conecta el detalle con la información del plato o bebida (como descripción, nombre e imagen).
