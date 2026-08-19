# Documentación del Controlador: PedidoController (Admin)

**Ruta física:** `app/Http/Controllers/Admin/PedidoController.php`

El `PedidoController` administra la auditoría e inspección general de comandas físicas y pedidos a domicilio en el panel de administrador, implementando la lógica de eliminación en cascada manual.

---

## 1. Código Fuente Completo

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    // Historial general de pedidos del sistema
    public function index()
    {
        $pedidos = Pedido::with(['user', 'mesa', 'mesero', 'detalles.producto'])->get();
        return view('admin.pedidos', compact('pedidos'));
    }

    // Actualiza el estado del pedido (En Espera / Entregado)
    public function updateStatus(Request $request, $id)
    {
        $pedido = Pedido::findOrFail($id);

        $request->validate([
            'estado' => 'required|string|in:En Espera,Entregado',
        ]);

        $pedido->update([
            'estado' => $request->estado
        ]);

        return redirect()->back()->with('success', 'Estado del pedido actualizado exitosamente.');
    }

    // Elimina el pedido y sus dependencias de factura de forma segura
    public function destroy($id)
    {
        $pedido = Pedido::findOrFail($id);

        // Eliminar la factura y sus detalles si existen para evitar violaciones de clave foránea (RESTRICT)
        if ($pedido->factura) {
            $pedido->factura->detalles()->delete();
            $pedido->factura->delete();
        }

        // Eliminar los detalles del pedido
        $pedido->detalles()->delete();

        // Eliminar el pedido de la base de datos
        $pedido->delete();

        return redirect()->back()->with('success', 'Pedido y su factura asociada eliminados exitosamente.');
    }
}
```

---

## 2. Explicación de la Lógica del Código

### 1. `destroy()` (Línea 32)
* **¿Por qué es crítico este método?** La base de datos tiene una relación restrictiva (`ON DELETE RESTRICT`) entre la tabla `facturas` y la tabla `pedidos` (a través del campo `pedido_id`). Si intentamos ejecutar una sentencia `DELETE` directa de SQL sobre un pedido que cuenta con una factura registrada, MySQL bloqueará la operación retornando una violación de restricción de clave foránea.
* **Solución por Código:** Este método intercepta la eliminación del pedido, comprueba si cuenta con un comprobante de facturación activo (`$pedido->factura`), y de ser así, remueve primero el desglose de productos de la factura (`detalles()->delete()`), luego la factura cabecera (`delete()`), seguidamente el desglose de la comanda (`$pedido->detalles()->delete()`) y por último el pedido principal. Esto asegura un borrado en cascada manual libre de excepciones.
