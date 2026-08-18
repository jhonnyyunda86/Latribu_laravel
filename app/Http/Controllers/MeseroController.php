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

    public function menu()
    {
        $categorias = Categoria::all();
        $productos = Producto::with('categoria')->get();
        // Obtener solo las mesas activas
        $mesas = Mesa::orderBy('numero')->get();

        return view('mesero.menu', compact('categorias', 'productos', 'mesas'));
    }

    public function storePedido(Request $request)
    {
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

        // Crear el pedido
        $pedido = Pedido::create([
            'user_id' => auth()->id(),
            'mesa_id' => $mesa->id,
            'mesero_id' => auth()->id(),
            'total' => $total,
            'estado' => 'En Espera',
            'tipo_pedido' => 'Mesa',
            'observaciones' => $request->observaciones,
        ]);

        // Registrar los detalles del pedido
        foreach ($pedidoItems as $detalle) {
            $pedido->detalles()->create($detalle);
            
            // Opcional: Reducir el stock del producto si es necesario
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
            'metodo_pago' => 'efectivo', // valor por defecto
            'estado_pago' => 'pendiente', // se mantiene como pendiente
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

        // Actualizar el estado de la mesa a Ocupada
        $mesa->update([
            'estado' => 'Ocupada'
        ]);

        return redirect()->route('mesero.dashboard')->with('success', '¡La comanda y su factura correspondiente han sido registradas con éxito!');
    }

    public function mesas()
    {
        // Obtener mesas con sus pedidos activos (En Espera)
        $mesas = Mesa::with(['pedidos' => function($q) {
                $q->where('estado', 'En Espera')->latest();
            }, 'pedidos.detalles.producto'])
            ->orderBy('numero')
            ->get();

        return view('mesero.mesas', compact('mesas'));
    }

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

    public function pedidos()
    {
        $pedidos = Pedido::with(['user', 'mesa', 'mesero', 'detalles.producto'])
            ->latest()
            ->get();

        return view('mesero.pedidos', compact('pedidos'));
    }

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

    public function reservas()
    {
        $reservas = Reserva::with(['user', 'mesa'])
            ->latest()
            ->get();

        return view('mesero.reservas', compact('reservas'));
    }

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
