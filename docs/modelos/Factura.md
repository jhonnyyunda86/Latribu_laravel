# Documentación del Modelo: Factura

**Ruta física:** `app/Models/Factura.php`

El modelo `Factura` gestiona los comprobantes y facturaciones de consumo de cada comanda o reserva concretada, asociando métodos de pago y totales facturados.

---

## 1. Código Fuente Explicado

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Factura extends Model
{
    // Atributos asignables masivamente
    protected $fillable = [
        'pedido_id',
        'numero_factura',
        'subtotal',
        'impuesto',
        'total',
        'metodo_pago',
        'estado_pago'
    ];

    // Conversión de tipos automática
    protected $casts = [
        'subtotal' => 'decimal:2',
        'impuesto' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    // Relación con el Pedido origen
    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    // Relación con los productos facturados (Desglose)
    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleFactura::class, 'factura_id');
    }
}
```

---

## 2. Explicación de la Lógica del Código

### 1. `$casts` (Línea 21)
Asegura que el `subtotal`, `impuesto` (IVA) y `total` financiero se manipulen en el código PHP como números decimales precisos (`decimal:2`), evitando pérdidas por redondeo de flotantes.

### 2. Relaciones Eloquent
* **`pedido()`**: Relación `BelongsTo` con `Pedido`. Vincula el comprobante de caja con la comanda o reserva que la originó.
* **`detalles()`**: Relación `HasMany` con `DetalleFactura`. Permite consultar el desglose exacto de los ítems impresos en el ticket al momento de realizar la transacción.
