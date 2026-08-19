# Documentación del Modelo: Categoria

**Ruta física:** `app/Models/Categoria.php`

El modelo `Categoria` organiza los platos, bebidas y mercancías del restaurante en agrupaciones lógicas en la carta digital (Ej: Hamburguesas, Bebidas, etc.).

---

## 1. Código Fuente Explicado

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends Model
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

    // Relación con los Productos de esta categoría
    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class, 'categoria_id');
    }
}
```

---

## 2. Explicación de la Lógica del Código

### 1. `$casts` (Línea 17)
* **`activo => boolean`**: Parsea el valor almacenado en base de datos (generalmente un entero `0` o `1` de tipo tinyint) a un tipo lógico booleano de PHP (`true` o `false`).

### 2. Relaciones Eloquent
* **`productos()`**: Relación `HasMany` con `Producto`. Facilita obtener la lista de todos los platos o bebidas que pertenecen a esta clasificación para renderizarlos agrupados en los menús de comandas del mesero y de compras del cliente.
