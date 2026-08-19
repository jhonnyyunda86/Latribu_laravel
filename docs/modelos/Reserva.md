# Documentación del Modelo: Reserva

**Ruta física:** `app/Models/Reserva.php`

El modelo `Reserva` es responsable de la gestión de la agenda del restaurante, permitiendo a los clientes programar con anticipación una mesa para su cena o almuerzo familiar.

---

## 1. Código Fuente Explicado

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reserva extends Model
{
    // 1. Atributos asignables masivamente
    protected $fillable = [
        'user_id',
        'mesa_id',
        'fecha_reserva',
        'hora_reserva',
        'cantidad_personas',
        'estado',
        'observaciones'
    ];

    // 2. Conversión automática de tipos
    protected $casts = [
        'cantidad_personas' => 'integer',
        'fecha_reserva' => 'date',
    ];

    // 3. Relación con el Cliente que reserva
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 4. Relación con la Mesa reservada
    public function mesa(): BelongsTo
    {
        return $this->belongsTo(Mesa::class, 'mesa_id');
    }

    // 5. MUTADOR: Guarda el estado de la reserva en minúsculas en Base de Datos
    public function setEstadoAttribute($value)
    {
        $this->attributes['estado'] = strtolower($value);
    }

    // 6. ACCESOR: Lee el estado capitalizado (Ej: Pendiente, Confirmada)
    public function getEstadoAttribute($value)
    {
        return ucfirst($value);
    }
}
```

---

## 2. Explicación de la Lógica del Código

### 1. `$fillable` (Línea 10)
Garantiza que la información crítica de la reserva (cliente, mesa, fecha, hora, comensales, estado e indicaciones) sea procesada y almacenada de forma masiva mediante peticiones HTTP.

### 2. `$casts` (Línea 21)
* **`cantidad_personas => integer`**: Convierte el número de invitados de texto a entero numérico.
* **`fecha_reserva => date`**: Convierte la cadena de texto de fecha (recibida del formulario) a una instancia de objeto `Carbon` de PHP. Esto facilita operaciones avanzadas con fechas (Ej: formatear como `d/m/Y`, sumar días o comparar con `today`).

### 3. Relaciones Eloquent
* **`user()`**: Relación `BelongsTo` que vincula la reserva con el perfil de usuario del cliente que la solicitó.
* **`mesa()`**: Relación `BelongsTo` que asocia el agendamiento con la mesa física del restaurante.

### 4. Mutadores y Accesores de Estado (`setEstadoAttribute` / `getEstadoAttribute`)
* **¿Para qué sirve?** La base de datos tiene una restricción `ENUM` en minúsculas (`pendiente`, `confirmada`, `cancelada`, `completada`).
* El mutador convierte automáticamente cualquier estado recibido (Ej: `'Confirmada'`) a minúscula (`'confirmada'`) antes de ejecutar el comando de inserción de MySQL.
* El accesor realiza la capitalización automática de la primera letra (Ej: de `'confirmada'` a `'Confirmada'`) al leerlo para su correcta visualización en las tablas e interfaces del mesero, administrador y cliente.
