# Documentación del Modelo: Producto

**Ruta física:** `app/Models/Producto.php`

El modelo `Producto` representa la ficha individual de cada alimento, bebida u otros artículos del catálogo de venta de la Tribu.

---

## 1. Código Fuente Explicado

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    // Atributos asignables masivamente
    protected $fillable = [
        'categoria_id',
        'nombre',
        'descripcion',
        'precio',
        'imagen',
        'disponible',
        'stock'
    ];

    // Conversión de tipos automática
    protected $casts = [
        'precio' => 'decimal:2',
        'disponible' => 'boolean',
        'stock' => 'integer',
    ];

    // Relación con la Categoría asociada
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }
}
```

---

## 2. Explicación de la Lógica del Código

### 1. `$casts` (Línea 21)
* **`precio => decimal:2`**: Garantiza precisión decimal en los costos de los productos.
* **`disponible => boolean`**: Convierte el estado de disponibilidad a boolean para validar y restringir compras de forma sencilla.
* **`stock => integer`**: Mapea la existencia de inventario en almacén a entero.

### 2. Relaciones Eloquent
* **`categoria()`**: Relación `BelongsTo` con `Categoria`. Vincula el producto con su correspondiente grupo clasificatorio del menú de comida.
