<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Inventario;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventarioController extends Controller
{
    public function index()
    {
        // Asegurar que exista la categoría específica
        $categoria = Categoria::firstOrCreate(
            ['nombre' => 'Bebidas en botella'],
            ['descripcion' => 'Inventario de bebidas en botella y enlatadas', 'activo' => true]
        );

        // Asegurar que exista al menos la bodega/inventario principal
        $bodegaPrincipal = Inventario::firstOrCreate(
            ['nombre' => 'Bodega Principal'],
            ['descripcion' => 'Bodega central de bebidas y licores', 'activo' => true]
        );

        $productos = Producto::where('categoria_id', $categoria->id)->get();
        $bodegas = Inventario::where('activo', true)->get();

        // Mostrar bitácora general de movimientos cruzando con inventario bodega origen
        $movimientos = MovimientoInventario::with(['producto', 'user', 'inventario'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('admin.inventario', compact('productos', 'movimientos', 'categoria', 'bodegas', 'bodegaPrincipal'));
    }

    public function storeProduct(Request $request)
    {
        // Registrar una nueva bebida en botella
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'inventario_id' => 'required|exists:inventarios,id',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        $categoria = Categoria::firstOrCreate(['nombre' => 'Bebidas en botella']);
        $validated['categoria_id'] = $categoria->id;
        $validated['disponible'] = true;

        if ($request->hasFile('imagen')) {
            $file = $request->file('imagen');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads'), $filename);
            $validated['imagen'] = '/uploads/' . $filename;
        }

        DB::transaction(function () use ($validated) {
            $producto = Producto::create($validated);

            // Crear movimiento inicial asignado a la bodega seleccionada
            MovimientoInventario::create([
                'inventario_id' => $validated['inventario_id'],
                'producto_id' => $producto->id,
                'user_id' => auth()->id(),
                'tipo_movimiento' => 'entrada',
                'cantidad' => $producto->stock,
                'observaciones' => 'Inventario inicial de producto registrado'
            ]);
        });

        return redirect()->back()->with('success', 'Bebida registrada y asociada al inventario correctamente.');
    }

    public function registrarMovimiento(Request $request, $id)
    {
        $validated = $request->validate([
            'tipo_movimiento' => 'required|in:entrada,salida',
            'inventario_id' => 'required|exists:inventarios,id',
            'cantidad' => 'required|integer|min:1',
            'observaciones' => 'nullable|string|max:255'
        ]);

        $producto = Producto::findOrFail($id);

        try {
            DB::transaction(function () use ($validated, $producto) {
                if ($validated['tipo_movimiento'] === 'entrada') {
                    $producto->increment('stock', $validated['cantidad']);
                } else {
                    if ($producto->stock < $validated['cantidad']) {
                        throw new \Exception('No hay suficiente stock disponible para realizar esta salida.');
                    }
                    $producto->decrement('stock', $validated['cantidad']);
                }

                MovimientoInventario::create([
                    'inventario_id' => $validated['inventario_id'],
                    'producto_id' => $producto->id,
                    'user_id' => auth()->id(),
                    'tipo_movimiento' => $validated['tipo_movimiento'],
                    'cantidad' => $validated['cantidad'],
                    'observaciones' => $validated['observaciones'] ?? 'Ajuste manual de inventario'
                ]);
            });
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([$e->getMessage()]);
        }

        $tipo = $validated['tipo_movimiento'] === 'entrada' ? 'Entrada registrada' : 'Salida registrada';
        return redirect()->back()->with('success', "{$tipo} correctamente en la bodega seleccionada.");
    }

    public function destroyProduct($id)
    {
        $producto = Producto::findOrFail($id);

        // Verificar si existen relaciones que impidan el borrado físico (por ej. pedidos, facturas o movimientos)
        $hasRelations = $producto->detallesPedido()->exists() || 
                        $producto->detallesFactura()->exists() || 
                        \App\Models\MovimientoInventario::where('producto_id', $producto->id)->exists();

        if ($hasRelations) {
            // En vez de eliminar físicamente, marcamos como no disponible
            $producto->update([
                'disponible' => false,
                'stock' => 0
            ]);

            return redirect()->back()->with('success', 'El producto tiene historial de ventas o movimientos de inventario. Se ha marcado como NO disponible en el menú en lugar de eliminarse.');
        }

        // Eliminar imagen local física si existe
        if ($producto->imagen && !str_starts_with($producto->imagen, 'http') && file_exists(public_path($producto->imagen))) {
            @unlink(public_path($producto->imagen));
        }

        $producto->delete();
        return redirect()->back()->with('success', 'Bebida eliminada del inventario correctamente.');
    }
}
