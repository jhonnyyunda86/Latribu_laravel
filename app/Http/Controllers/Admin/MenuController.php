<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $categorias = Categoria::all();
        $productos = Producto::with('categoria')->get();
        return view('admin.menu', compact('categorias', 'productos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'disponible' => 'boolean'
        ]);

        $validated['disponible'] = $request->has('disponible');

        if ($request->hasFile('imagen')) {
            $file = $request->file('imagen');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads'), $filename);
            $validated['imagen'] = '/uploads/' . $filename;
        }

        Producto::create($validated);

        return redirect()->back()->with('success', 'Producto creado exitosamente.');
    }

    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        $validated = $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'disponible' => 'boolean'
        ]);

        $validated['disponible'] = $request->has('disponible');

        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior local si existe y no es una URL externa
            if ($producto->imagen && !str_starts_with($producto->imagen, 'http') && file_exists(public_path($producto->imagen))) {
                @unlink(public_path($producto->imagen));
            }
            
            $file = $request->file('imagen');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads'), $filename);
            $validated['imagen'] = '/uploads/' . $filename;
        } else {
            // Si no se sube una nueva imagen, conservamos la actual
            unset($validated['imagen']);
        }

        $producto->update($validated);

        return redirect()->back()->with('success', 'Producto actualizado exitosamente.');
    }

    public function destroy($id)
    {
        $producto = Producto::findOrFail($id);
        
        // Eliminar imagen local física si existe
        if ($producto->imagen && !str_starts_with($producto->imagen, 'http') && file_exists(public_path($producto->imagen))) {
            @unlink(public_path($producto->imagen));
        }

        $producto->delete();

        return redirect()->back()->with('success', 'Producto eliminado exitosamente.');
    }
}
