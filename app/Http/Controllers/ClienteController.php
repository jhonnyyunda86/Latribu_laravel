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
    public function dashboard()
    {
        // Obtener estadísticas reales del cliente actual
        $userId = auth()->id();

        $reservasActivasCount = Reserva::where('user_id', $userId)
            ->where('estado', 'Confirmada')
            ->count();

        $totalPedidosCount = Pedido::where('user_id', $userId)->count();

        $reservas = Reserva::where('user_id', $userId)->with('mesa')->latest()->get();

        return view('clientes.dashboard', compact('reservasActivasCount', 'totalPedidosCount', 'reservas'));
    }

    public function facturas()
    {
        // Obtener las facturas del cliente a través de sus pedidos
        $facturas = Factura::whereHas('pedido', function ($q) {
            $q->where('user_id', auth()->id());
        })
        ->with(['pedido.mesa', 'detalles'])
        ->latest()
        ->get();

        return view('clientes.facturas', compact('facturas'));
    }

    public function menu()
    {
        $categorias = Categoria::all();
        $productos = Producto::with('categoria')->get();
        return view('clientes.menu', compact('categorias', 'productos'));
    }

    public function storePedido(Request $request)
    {
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

        // Crear el pedido de tipo domicilio
        $pedido = Pedido::create([
            'user_id' => auth()->id(),
            'mesa_id' => null, // Domicilio no tiene mesa
            'mesero_id' => null, // Domicilio online no tiene mesero asignado inicialmente
            'total' => $total,
            'estado' => 'En Espera', // se mapea a 'pendiente' en la base de datos por nuestro mutador
            'tipo_pedido' => 'domicilio',
            'observaciones' => $comentarios,
        ]);

        // Registrar los detalles del pedido
        foreach ($pedidoItems as $detalle) {
            $pedido->detalles()->create($detalle);

            // Reducir stock del producto
            $producto = Producto::find($detalle['producto_id']);
            if ($producto && $producto->stock >= $detalle['cantidad']) {
                $producto->decrement('stock', $detalle['cantidad']);
            }
        }

        // Crear la factura correspondiente al pedido automáticamente
        $factura = Factura::create([
            'pedido_id' => $pedido->id,
            'numero_factura' => 'FAC-' . strtoupper(uniqid()),
            'subtotal' => $total,
            'impuesto' => 0.00,
            'total' => $total,
            'metodo_pago' => 'transferencia', // Por defecto transferencia/online
            'estado_pago' => 'pendiente',
        ]);

        // Registrar los detalles de la factura
        foreach ($pedidoItems as $detalle) {
            $factura->detalles()->create([
                'producto_id' => $detalle['producto_id'],
                'nombre_producto' => Producto::find($detalle['producto_id'])->nombre ?? 'Desconocido',
                'cantidad' => $detalle['cantidad'],
                'precio_unitario' => $detalle['precio_unitario'],
                'subtotal' => $detalle['subtotal'],
            ]);
        }

        return redirect()->route('cliente.dashboard')->with('success', '¡Tu pedido a Domicilio y su factura correspondiente han sido registrados con éxito! Te mantendremos informado.');
    }

    public function descargarPdf($id)
    {
        $factura = Factura::whereHas('pedido', function($q) {
            $q->where('user_id', auth()->id());
        })
        ->with(['pedido.mesa', 'detalles'])
        ->findOrFail($id);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('clientes.factura_pdf', compact('factura'));

        // Custom POS ticket size: 80mm width (~226pt) x 650pt height
        $pdf->setPaper([0, 0, 226, 650], 'portrait');

        return $pdf->download("Factura-{$factura->numero_factura}.pdf");
    }

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

        // Crear la reserva
        $reserva = Reserva::create([
            'user_id' => auth()->id(),
            'mesa_id' => $mesa->id,
            'fecha_reserva' => $request->fecha_reserva,
            'hora_reserva' => $request->hora_reserva,
            'cantidad_personas' => $request->cantidad_personas,
            'estado' => 'Pendiente', // se guarda como 'pendiente' en DB por mutador
            'observaciones' => $request->observaciones,
        ]);

        // Crear el pedido correspondiente al cobro de reserva
        $pedido = Pedido::create([
            'user_id' => auth()->id(),
            'mesa_id' => $mesa->id,
            'mesero_id' => null,
            'total' => 0.00, // Costo de reserva de $0 por defecto
            'estado' => 'En Espera', // mapea a 'pendiente'
            'tipo_pedido' => 'mesa',
            'observaciones' => "Reserva Mesa #{$mesa->numero} para el {$reserva->fecha_reserva->format('d/m/Y')} a las {$reserva->hora_reserva}",
        ]);

        // Crear la factura de reserva de $0 automáticamente
        $factura = Factura::create([
            'pedido_id' => $pedido->id,
            'numero_factura' => 'FAC-RES-' . strtoupper(uniqid()),
            'subtotal' => 0.00,
            'impuesto' => 0.00,
            'total' => 0.00,
            'metodo_pago' => 'otros',
            'estado_pago' => 'pendiente',
        ]);

        return redirect()->route('cliente.reservas')->with('success', '¡Tu reserva ha sido solicitada con éxito! Su comprobante de reserva ha sido generado en facturas.');
    }

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
