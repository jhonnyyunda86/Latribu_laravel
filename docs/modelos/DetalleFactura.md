# Documentación del Modelo: DetalleFactura

**Ruta física:** `app/Models/DetalleFactura.php`

El modelo `DetalleFactura` almacena la información histórica e inalterable de los productos cobrados en una factura.

---

## 1. Código Fuente Explicado

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleFactura extends Model
{
    // Nombre de la tabla física
    protected $table = 'detalle_facturas';

    // Atributos asignables masivamente
    protected $fillable = [
        'factura_id',
        'producto_id',
        'nombre_producto',
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

    // Relación con la Factura correspondiente
    public function factura(): BelongsTo
    {
        return $this->belongsTo(Factura::class, 'factura_id');
    }

    // Relación con el Producto facturado
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
```

---

## 2. Explicación de la Lógica del Código

### 1. `nombre_producto` (Línea 15)
* **¿Para qué sirve?** Copia el nombre del producto en el instante preciso del cobro de la factura. Esto se hace para mantener la integridad histórica del comprobante: si en el futuro el administrador edita el nombre de una hamburguesa en el catálogo de productos, la factura original conservará el nombre histórico que el cliente compró en ese momento.

### 2. Relaciones Eloquent
* **`factura()`**: Relación `BelongsTo` con `Factura`.
* **`producto()`**: Relación `BelongsTo` con `Producto`.
