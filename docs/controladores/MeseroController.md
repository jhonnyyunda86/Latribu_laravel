# Documentación del Controlador: MeseroController

**Ruta física:** `app/Http/Controllers/MeseroController.php`

El `MeseroController` gestiona toda la interfaz de operaciones del personal del salón: monitor de ocupación de mesas, toma de comandas físicas, monitor de preparación en cocina e histórico de reservaciones.

---

## 1. Código Fuente Completo

```php
<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\Reserva;
use App\Models\Factura;
use Illuminate\Http\Request;

class MeseroController extends Controller
{
    // Carga el panel principal de mesas del mesero
    public function dashboard()
    {
        $mesas = Mesa::orderBy('numero')->get();
        $totalPedidosActivos = Pedido::where('mesero_id', auth()->id())
            ->where('estado', 'En Espera')
            ->count();
        $totalEntregas = Pedido::where('mesero_id', auth()->id())
            ->where('estado', 'Entregado')
            ->count();

        return view('mesero.dashboard', compact('mesas', 'totalPedidosActivos', 'totalEntregas'));
    }

    // Carga la carta digital y las mesas activas para tomar pedidos
    public function menu()
    {
        $categorias = Categoria::all();
        $productos = Producto::with('categoria')->get();
        $mesas = Mesa::orderBy('numero')->get();

        return view('mesero.menu', compact('categorias', 'productos', 'mesas'));
    }

    // Registra una nueva comanda, genera la factura y reduce stock
    public function storePedido(Request $request)
    {
        // 1. Validación de datos de entrada
        $request->validate([
            'mesa_id' => 'required|exists:mesas,id',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'observaciones' => 'nullable|string',
        ]);

        $mesa = Mesa::findOrFail($request->mesa_id);
        
        $total = 0;
        $pedidoItems = [];

        // 2. Procesamiento de totales y desglose de items
        foreach ($request->productos as $item) {
            $producto = Producto::findOrFail($item['id']);
            $cantidad = (int) $item['cantidad'];
            $precioUnitario = (float) $producto->precio;
            $subtotal = $precioUnitario * $cantidad;
            $total += $subtotal;
            
            $pedidoItems[] = [
                'producto_id' => $producto->id,
                'cantidad' => $cantidad,
                'precio_unitario' => $precioUnitario,
                'subtotal' => $subtotal,
            ];
        }

        // 3. Creación del Pedido principal
        $pedido = Pedido::create([
            'user_id' => auth()->id(),
            'mesa_id' => $mesa->id,
            'mesero_id' => auth()->id(),
            'total' => $total,
            'estado' => 'En Espera',
            'tipo_pedido' => 'Mesa',
            'observaciones' => $request->observaciones,
        ]);

        // 4. Registro del desglose y descuento de stock
        foreach ($pedidoItems as $detalle) {
            $pedido->detalles()->create($detalle);
            
            $producto = Producto::find($detalle['producto_id']);
            if ($producto && $producto->stock >= $detalle['cantidad']) {
                $producto->decrement('stock', $detalle['cantidad']);
            }
        }

        // 5. Creación automática de la Factura
        $factura = Factura::create([
            'pedido_id' => $pedido->id,
            'numero_factura' => 'FAC-' . strtoupper(uniqid()),
            'subtotal' => $total,
            'impuesto' => 0.00,
            'total' => $total,
            'metodo_pago' => 'efectivo',
            'estado_pago' => 'pendiente',
        ]);

        // 6. Registro de detalles de la Factura
        foreach ($pedidoItems as $detalle) {
            $factura->detalles()->create([
                'producto_id' => $detalle['producto_id'],
                'nombre_producto' => Producto::find($detalle['producto_id'])->nombre ?? 'Desconocido',
                'cantidad' => $detalle['cantidad'],
                'precio_unitario' => $detalle['precio_unitario'],
                'subtotal' => $detalle['subtotal'],
            ]);
        }

        // 7. Actualización del estado de la mesa física
        $mesa->update([
            'estado' => 'Ocupada'
        ]);

        return redirect()->route('mesero.dashboard')->with('success', '¡La comanda y su factura correspondiente han sido registradas con éxito!');
    }

    // Lista el salón comedor con consumos
    public function mesas()
    {
        $mesas = Mesa::with(['pedidos' => function($q) {
                $q->where('estado', 'En Espera')->latest();
            }, 'pedidos.detalles.producto'])
            ->orderBy('numero')
            ->get();

        return view('mesero.mesas', compact('mesas'));
    }

    // Cambia el estado de una mesa mediante modal
    public function updateMesaStatus(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|string|in:Disponible,Ocupada,Reservada,Cuenta,Mantenimiento',
        ]);

        $mesa = Mesa::findOrFail($id);
        $oldStatus = $mesa->estado;
        $mesa->update([
            'estado' => $request->estado
        ]);

        return redirect()->back()->with('success', "Mesa {$mesa->numero} cambiada de {$oldStatus} a {$request->estado} correctamente.");
    }

    // Monitor de comandas de cocina
    public function pedidos()
    {
        $pedidos = Pedido::with(['user', 'mesa', 'mesero', 'detalles.producto'])
            ->latest()
            ->get();

        return view('mesero.pedidos', compact('pedidos'));
    }

    // Cambia el estado de despacho de pedidos
    public function updatePedidoStatus(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|string|in:En Espera,Entregado',
        ]);

        $pedido = Pedido::findOrFail($id);
        $pedido->update([
            'estado' => $request->estado
        ]);

        return redirect()->back()->with('success', "Estado del pedido #{$pedido->id} cambiado a {$request->estado} correctamente.");
    }

    // Agenda de reservas del día
    public function reservas()
    {
        $reservas = Reserva::with(['user', 'mesa'])
            ->latest()
            ->get();

        return view('mesero.reservas', compact('reservas'));
    }

    // Cambia el estado de confirmación de reservas
    public function updateReservaStatus(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|string|in:Pendiente,Confirmada,Cancelada',
        ]);

        $reserva = Reserva::findOrFail($id);
        $reserva->update([
            'estado' => $request->estado
        ]);

        return redirect()->back()->with('success', "Estado de la reserva cambiado a {$request->estado} correctamente.");
    }
}
```

---

## 2. Explicación de la Lógica del Código

### 1. `dashboard()` (Línea 16)
Obtiene todas las mesas físicas de la base de datos ordenadas secuencialmente, y calcula los pedidos activos en espera e historial de entregas del mesero autenticado en sesión para alimentar las tarjetas métricas del panel.

### 2. `storePedido()` (Línea 38)
Este es el flujo crítico de comandas físicas:
* **Validación:** Comprueba la existencia de la mesa y que el array de productos contenga elementos válidos y cantidades numéricas superiores a cero.
* **Cálculo Financiero:** Recorre los productos seleccionados, lee sus precios y calcula el monto total acumulado.
* **Reducción de Inventario:** Descuenta las porciones vendidas del stock actual del producto (`decrement('stock', cantidad)`).
* **Facturación Automática:** Registra de inmediato la `Factura` del consumo en estado `pendiente` con su correspondiente copia histórica de nombres de productos (`DetalleFactura`), asegurando que la venta quede registrada financieramente al instante.
* **Estatus de Mesa:** Modifica el estatus de la mesa a `Ocupada` para reflejarse visualmente en la cuadrícula de monitores.

### 3. `mesas()` (Línea 120)
Recupera la cuadrícula del comedor realizando un filtrado ansioso (`Eager Loading`) de los pedidos en estado `'En Espera'` y sus respectivos alimentos agregados, optimizando las consultas SQL a la base de datos (evitando el problema N+1).

### 4. `updateMesaStatus()` (Línea 132)
Permite al mesero actualizar el estatus de la mesa (por ejemplo, cambiar a `'Cuenta'` cuando el cliente solicita pagar, o `'Disponible'` tras retirarse).
