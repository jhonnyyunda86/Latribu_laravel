# Documentación del Modelo: Mesa

**Ruta física:** `app/Models/Mesa.php`

El modelo `Mesa` representa cada mesa física disponible en el comedor del restaurante. Permite controlar su estatus y vincular pedidos y reservas activas.

---

## 1. Código Fuente Explicado

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mesa extends Model
{
    // Atributos asignables masivamente
    protected $fillable = [
        'numero',
        'capacidad',
        'estado'
    ];

    // Relación con los Pedidos consumidos en la mesa
    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class, 'mesa_id');
    }

    // Relación con las Reservas programadas
    public function reservas(): HasMany
    {
        return $this->hasMany(Reserva::class, 'mesa_id');
    }
}
```

---

## 2. Explicación de la Lógica del Código

### 1. `$fillable` (Línea 10)
Permite ingresar de forma masiva el número de la mesa, el total de comensales que soporta y su estado actual de ocupación.

### 2. Estado de Mesa (`estado`)
El estado de la mesa puede variar entre `Disponible`, `Ocupada`, `Reservada`, `Cuenta` y `Mantenimiento`.

### 3. Relaciones Eloquent
* **`pedidos()`**: Relación `HasMany` con `Pedido`. Permite consultar todos los consumos (órdenes) históricos y actuales que se han servido en esta mesa.
* **`reservas()`**: Relación `HasMany` con `Reserva`. Permite auditar la agenda y cronograma de reservas agendadas específicamente para esta mesa física.
