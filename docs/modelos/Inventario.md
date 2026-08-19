# Documentación del Modelo: Inventario

**Ruta física:** `app/Models/Inventario.php`

El modelo `Inventario` administra las bodegas y almacenes físicos disponibles en el restaurante para llevar el control de stock.

---

## 1. Código Fuente Explicado

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventario extends Model
{
    // Atributos asignables masivamente
    protected $fillable = [
        'nombre',
        'descripcion',
        'activo'
    ];

    // Conversión de tipos automática
    protected $casts = [
        'activo' => 'boolean',
    ];

    // Relación con los Movimientos ocurridos en esta bodega
    public function movimientos(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class, 'inventario_id');
    }
}
```

---

## 2. Explicación de la Lógica del Código

### 1. `$casts` (Línea 17)
* **`activo => boolean`**: Parsea la disponibilidad o estatus activo de la bodega.

### 2. Relaciones Eloquent
* **`movimientos()`**: Relación `HasMany` con `MovimientoInventario`. Vincula la bodega con la bitácora de entradas, salidas y ajustes manuales que se han ejecutado en ella para efectos de auditoría.
