# Documentación del Controlador: MenuController

**Ruta física:** `app/Http/Controllers/Admin/MenuController.php`

El `MenuController` es responsable de la administración del catálogo de comidas y bebidas ofrecidas en el restaurante. Permite registrar nuevos platillos, actualizar sus datos, cambiar precios y subir o borrar imágenes.

---

## 1. Código Fuente Completo

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    // Carga las categorías y productos activos
    public function index()
    {
        $categorias = Categoria::all();
        $productos = Producto::with('categoria')->get();
        return view('admin.menu', compact('categorias', 'productos'));
    }

    // Crea un nuevo producto y guarda su imagen
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

        // Procesamiento de la imagen cargada
        if ($request->hasFile('imagen')) {
            $file = $request->file('imagen');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads'), $filename);
            $validated['imagen'] = '/uploads/' . $filename;
        }

        Producto::create($validated);

        return redirect()->back()->with('success', 'Producto creado exitosamente.');
    }

    // Actualiza la información del producto e imagen
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

        // Reemplazar imagen antigua si se sube una nueva
        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior local del disco si existe
            if ($producto->imagen && !str_starts_with($producto->imagen, 'http') && file_exists(public_path($producto->imagen))) {
                @unlink(public_path($producto->imagen));
            }
            
            $file = $request->file('imagen');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads'), $filename);
            $validated['imagen'] = '/uploads/' . $filename;
        } else {
            // Conservar la imagen actual si no se proporciona una nueva
            unset($validated['imagen']);
        }

        $producto->update($validated);

        return redirect()->back()->with('success', 'Producto actualizado exitosamente.');
    }

    // Elimina el producto y remueve su archivo de imagen físico
    public function destroy($id)
    {
        $producto = Producto::findOrFail($id);
        
        // Eliminar imagen física del disco
        if ($producto->imagen && !str_starts_with($producto->imagen, 'http') && file_exists(public_path($producto->imagen))) {
            @unlink(public_path($producto->imagen));
        }

        $producto->delete();

        return redirect()->back()->with('success', 'Producto eliminado exitosamente.');
    }
}
```

---

## 2. Explicación de la Lógica del Código

### 1. `store()` (Línea 19)
* Procesa los datos del formulario de creación.
* **Carga de Archivos:** Verifica la presencia de una imagen (`hasFile`). Genera un nombre de archivo único utilizando la marca de tiempo actual (`time()`) concatenado con el nombre original del archivo para evitar colisiones de nombres. Mueve el archivo a la carpeta pública `/uploads` y guarda esa ruta en la base de datos.

### 2. `update()` (Línea 44)
* Modifica la información del producto.
* Si el administrador proporciona una nueva fotografía, el método elimina la imagen anterior usando la función de bajo nivel de PHP `@unlink` para evitar la acumulación de archivos inútiles en el almacenamiento del servidor, y luego sube la nueva.

### 3. `destroy()` (Línea 79)
* Elimina el registro del producto en la base de datos.
* Al igual que en la edición, se asegura de purgar del disco duro el archivo físico de la imagen asociada para mantener el servidor optimizado.
