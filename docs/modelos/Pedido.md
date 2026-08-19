# Documentación del Modelo: Pedido

**Ruta física:** `app/Models/Pedido.php`

El modelo `Pedido` es una de las piezas centrales del sistema de facturación y comandas del restaurante. Permite asociar las órdenes de comida con las mesas, meseros, clientes, productos solicitados y la factura de cobro.

---

## 1. Código Fuente Explicado

A continuación se detalla el código del modelo y la explicación de sus secciones más importantes:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pedido extends Model
{
    // 1. Atributos asignables masivamente (Mass Assignment)
    protected $fillable = [
        'user_id',
        'mesa_id',
        'mesero_id',
        'total',
        'estado',
        'tipo_pedido',
        'observaciones'
    ];

    // 2. Conversión de tipos (Casting)
    protected $casts = [
        'total' => 'decimal:2',
    ];

    // 3. Relación con el Cliente
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 4. Relación con la Mesa
    public function mesa(): BelongsTo
    {
        return $this->belongsTo(Mesa::class, 'mesa_id');
    }

    // 5. Relación con el Mesero
    public function mesero(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mesero_id');
    }

    // 6. Relación con los Platillos Ordenados (Desglose)
    public function detalles(): HasMany
    {
        return $this->hasMany(DetallePedido::class, 'pedido_id');
    }

    // 7. Relación con la Factura de Cobro
    public function factura(): HasOne
    {
        return $this->hasOne(Factura::class, 'pedido_id');
    }

    // 8. MUTADOR: Guarda el estado en minúsculas en Base de Datos
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

    // 9. ACCESOR: Lee el estado en formato amigable para el frontend
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

    // 10. MUTADOR: Guarda tipo_pedido en minúsculas
    public function setTipoPedidoAttribute($value)
    {
        $this->attributes['tipo_pedido'] = strtolower($value);
    }

    // 11. ACCESOR: Lee tipo_pedido capitalizado (Ej: Mesa, Domicilio)
    public function getTipoPedidoAttribute($value)
    {
        return ucfirst($value);
    }
}
```

---

## 2. Explicación de la Lógica del Código

### 1. `$fillable` (Línea 12)
Especifica qué campos de la tabla `pedidos` se pueden guardar o modificar mediante arreglos asociativos en PHP (Ej: `Pedido::create($data)`). Esto protege al servidor contra vulnerabilidades de inyección de campos no autorizados.

### 2. `$casts` (Línea 22)
Fuerza a Laravel a convertir el atributo `total` a un formato decimal de dos dígitos (`decimal:2`) de forma automática cada vez que se consulte, asegurando precisión financiera.

### 3. Relaciones Eloquent (`user`, `mesa`, `mesero`, `detalles`, `factura`)
* **`belongsTo(User::class, 'user_id')`**: Indica que el pedido pertenece a un cliente específico de la tabla `users`.
* **`belongsTo(Mesa::class, 'mesa_id')`**: Vincula el pedido con la mesa física del salón donde se consume.
* **`hasMany(DetallePedido::class, 'pedido_id')`**: Conecta el pedido con el listado detallado de platos y porciones ordenadas.
* **`hasOne(Factura::class, 'pedido_id')`**: Enlaza el pedido directamente con su comprobante de pago electrónico.

### 4. Mutadores y Accesores de Estado (`setEstadoAttribute` / `getEstadoAttribute`)
* **¿Para qué sirve?** La base de datos tiene una restricción estricta de tipo `ENUM` con los valores en minúscula (`pendiente`, `entregado`, `cancelado`). El mutador traduce de forma invisible textos como `'En Espera'` a `'pendiente'` antes de realizar el `INSERT` o `UPDATE` de SQL, previniendo errores de truncado (`Data truncated warning`).
* El accesor realiza el proceso contrario al renderizar: traduce `'pendiente'` de la base de datos de vuelta a `'En Espera'` para que el mesero y el cliente lo vean formateado de manera elegante en el frontend.
