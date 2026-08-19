# Documentación del Controlador: ClienteController

**Ruta física:** `app/Http/Controllers/ClienteController.php`

El `ClienteController` expone la lógica y funcionalidades accesibles desde el panel de clientes: carta interactiva digital para pedidos a domicilio, solicitud de reservas, historial de consumos y descargas de facturas en formato PDF POS.

---

## 1. Código Fuente Completo

```php
<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\Pedido;
use App\Models\Reserva;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Mesa;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    // Carga estadísticas de reservas y pedidos en el panel de inicio del cliente
    public function dashboard()
    {
        $userId = auth()->id();

        $reservasActivasCount = Reserva::where('user_id', $userId)
            ->where('estado', 'Confirmada')
            ->count();

        $totalPedidosCount = Pedido::where('user_id', $userId)->count();

        $reservas = Reserva::where('user_id', $userId)->with('mesa')->latest()->get();

        return view('clientes.dashboard', compact('reservasActivasCount', 'totalPedidosCount', 'reservas'));
    }

    // Listado de facturas asociadas a las compras de este cliente
    public function facturas()
    {
        $facturas = Factura::whereHas('pedido', function ($q) {
            $q->where('user_id', auth()->id());
        })
        ->with(['pedido.mesa', 'detalles'])
        ->latest()
        ->get();

        return view('clientes.facturas', compact('facturas'));
    }

    // Carta digital de productos e ingredientes
    public function menu()
    {
        $categorias = Categoria::all();
        $productos = Producto::with('categoria')->get();
        return view('clientes.menu', compact('categorias', 'productos'));
    }

    // Procesa el pedido a domicilio, descuenta stock y genera factura
    public function storePedido(Request $request)
    {
        // 1. Validación de envío
        $request->validate([
            'direccion' => 'required|string|max:255',
            'telefono' => 'required|string|max:50',
            'observaciones' => 'nullable|string',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
        ]);

        $total = 0;
        $pedidoItems = [];

        // 2. Cálculo financiero e items
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

        $comentarios = "Dirección: " . $request->direccion . " | Tel: " . $request->telefono;
        if ($request->observaciones) {
            $comentarios .= " | Nota: " . $request->observaciones;
        }

        // 3. Creación del pedido
        $pedido = Pedido::create([
            'user_id' => auth()->id(),
            'mesa_id' => null, // null ya que es domicilio
            'mesero_id' => null,
            'total' => $total,
            'estado' => 'En Espera',
            'tipo_pedido' => 'domicilio',
            'observaciones' => $comentarios,
        ]);

        // 4. Detalles del pedido y descuento de stock
        foreach ($pedidoItems as $detalle) {
            $pedido->detalles()->create($detalle);

            $producto = Producto::find($detalle['producto_id']);
            if ($producto && $producto->stock >= $detalle['cantidad']) {
                $producto->decrement('stock', $detalle['cantidad']);
            }
        }

        // 5. Creación automática de factura
        $factura = Factura::create([
            'pedido_id' => $pedido->id,
            'numero_factura' => 'FAC-' . strtoupper(uniqid()),
            'subtotal' => $total,
            'impuesto' => 0.00,
            'total' => $total,
            'metodo_pago' => 'transferencia',
            'estado_pago' => 'pendiente',
        ]);

        // 6. Registro de detalle de la factura
        foreach ($pedidoItems as $detalle) {
            $factura->detalles()->create([
                'producto_id' => $detalle['producto_id'],
                'nombre_producto' => Producto::find($detalle['producto_id'])->nombre ?? 'Desconocido',
                'cantidad' => $detalle['cantidad'],
                'precio_unitario' => $detalle['precio_unitario'],
                'subtotal' => $detalle['subtotal'],
            ]);
        }

        return redirect()->route('cliente.dashboard')->with('success', '¡Tu pedido a Domicilio y su factura correspondiente han sido registrados con éxito!');
    }

    // Genera el archivo PDF POS de 80mm de ancho y descarga
    public function descargarPdf($id)
    {
        $factura = Factura::whereHas('pedido', function($q) {
            $q->where('user_id', auth()->id());
        })
        ->with(['pedido.mesa', 'detalles'])
        ->findOrFail($id);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('clientes.factura_pdf', compact('factura'));

        // Configuración de papel tipo POS: 80mm ancho x 650pt alto
        $pdf->setPaper([0, 0, 226, 650], 'portrait');

        return $pdf->download("Factura-{$factura->numero_factura}.pdf");
    }

    // Listado de mesas e historial de reservas del cliente
    public function reservas()
    {
        $userId = auth()->id();
        $reservas = Reserva::where('user_id', $userId)
            ->with('mesa')
            ->latest()
            ->get();
            
        $mesas = Mesa::orderBy('numero')->get();

        return view('clientes.reservas', compact('reservas', 'mesas'));
    }

    // Agenda una reserva de mesa y genera comanda y factura de respaldo
    public function storeReserva(Request $request)
    {
        $request->validate([
            'mesa_id' => 'required|exists:mesas,id',
            'fecha_reserva' => 'required|date|after_or_equal:today',
            'hora_reserva' => 'required',
            'cantidad_personas' => 'required|integer|min:1',
            'observaciones' => 'nullable|string',
        ]);

        $mesa = Mesa::findOrFail($request->mesa_id);

        // 1. Crear registro de reserva
        $reserva = Reserva::create([
            'user_id' => auth()->id(),
            'mesa_id' => $mesa->id,
            'fecha_reserva' => $request->fecha_reserva,
            'hora_reserva' => $request->hora_reserva,
            'cantidad_personas' => $request->cantidad_personas,
            'estado' => 'Pendiente',
            'observaciones' => $request->observaciones,
        ]);

        // 2. Crear pedido ficticio de respaldo ($0.00)
        $pedido = Pedido::create([
            'user_id' => auth()->id(),
            'mesa_id' => $mesa->id,
            'mesero_id' => null,
            'total' => 0.00,
            'estado' => 'En Espera',
            'tipo_pedido' => 'mesa',
            'observaciones' => "Reserva Mesa #{$mesa->numero} para el {$reserva->fecha_reserva->format('d/m/Y')} a las {$reserva->hora_reserva}",
        ]);

        // 3. Crear factura de $0.00
        $factura = Factura::create([
            'pedido_id' => $pedido->id,
            'numero_factura' => 'FAC-RES-' . strtoupper(uniqid()),
            'subtotal' => 0.00,
            'impuesto' => 0.00,
            'total' => 0.00,
            'metodo_pago' => 'otros',
            'estado_pago' => 'pendiente',
        ]);

        return redirect()->route('cliente.reservas')->with('success', '¡Tu reserva ha sido solicitada con éxito!');
    }

    // Historial de compras/pedidos del cliente
    public function pedidos()
    {
        $userId = auth()->id();
        $pedidos = Pedido::where('user_id', $userId)
            ->with(['mesa', 'detalles.producto', 'factura'])
            ->latest()
            ->get();

        return view('clientes.pedidos', compact('pedidos'));
    }
}
```

---

## 2. Explicación de la Lógica del Código

### 1. `storePedido()` (Línea 51)
Procesa la compra en línea realizada por el cliente:
* Valida dirección y teléfono de despacho.
* Registra el pedido en base de datos especificando el tipo como `domicilio`.
* Descuenta existencias del catálogo de inventario.
* Crea de manera automática la factura de cobro asociada en estado `pendiente`.

### 2. `descargarPdf()` (Línea 132)
* Recupera la factura solicitada garantizando que corresponda al cliente actualmente autenticado (evita accesos no autorizados a facturas de otros clientes).
* Carga la vista limpia [`factura_pdf.blade.php`](file:///c:/laragon/www/la_tribu/resources/views/clientes/factura_pdf.blade.php).
* **Tamaño del Papel:** Ajusta las dimensiones en puntos tipográficos (`[0, 0, 226, 650]`) equivalentes a los 80 milímetros estándar de ancho de los rollos de papel térmico de las impresoras de tickets POS de cocina y caja.

### 3. `storeReserva()` (Línea 161)
* Crea la reserva para la fecha y hora seleccionada.
* **Lógica de Facturación Obligatoria:** Dado que el diseño relacional de la base de datos restringe la creación de una factura si esta no apunta a un pedido (`pedido_id` no nulo), el método crea un pedido ficticio de respaldo por valor de `$0.00` que almacena los metadatos de la reserva, permitiendo emitir el ticket de reserva correspondiente de manera limpia.
