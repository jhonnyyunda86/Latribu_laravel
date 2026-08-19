# Documentación del Modelo: MovimientoInventario

**Ruta física:** `app/Models/MovimientoInventario.php`

El modelo `MovimientoInventario` representa los registros en la bitácora de auditoría que explican por qué incrementaron o decrementaron existencias de productos en una bodega del restaurante.

---

## 1. Código Fuente Explicado

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimientoInventario extends Model
{
    // Nombre de la tabla física
    protected $table = 'movimientos_inventario';

    // Atributos asignables masivamente
    protected $fillable = [
        'inventario_id',
        'producto_id',
        'user_id',
        'tipo_movimiento',
        'cantidad',
        'observaciones'
    ];

    // Conversión de tipos automática
    protected $casts = [
        'cantidad' => 'integer',
    ];

    // Relación con la Bodega / Inventario de origen
    public function inventario(): BelongsTo
    {
        return $this->belongsTo(Inventario::class, 'inventario_id');
    }

    // Relación con el Producto afectado
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    // Relación con el Administrador / Empleado que ejecuta la acción
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
```

---

## 2. Explicación de la Lógica del Código

### 1. `$table` (Línea 10)
Obliga a Eloquent a conectarse a la tabla física `movimientos_inventario` en lugar del plural automático `movimiento_inventarios`.

### 2. Relaciones Eloquent
* **`inventario()`**: Relación `BelongsTo` con `Inventario`. Vincula el movimiento con la bodega afectada.
* **`producto()`**: Relación `BelongsTo` con `Producto`. Indica qué bebida o insumo sufrió la variación de cantidad.
* **`user()`**: Relación `BelongsTo` con `User`. Registra la identidad del administrador que llevó a cabo el ajuste del stock.
