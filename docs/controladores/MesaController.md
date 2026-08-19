# Documentación del Controlador: MesaController

**Ruta física:** `app/Http/Controllers/Admin/MesaController.php`

El `MesaController` gestiona las mesas del local, controlando su capacidad física, número identificador y estado de disponibilidad en el salón del restaurante.

---

## 1. Código Fuente Completo

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mesa;
use Illuminate\Http\Request;

class MesaController extends Controller
{
    // Carga todas las mesas registradas
    public function index()
    {
        $mesas = Mesa::all();
        return view('admin.mesas', compact('mesas'));
    }

    // Registra una nueva mesa
    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero' => 'required|string|max:50|unique:mesas,numero',
            'capacidad' => 'required|integer|min:1',
            'estado' => 'required|string|in:Disponible,Ocupada,Reservada,Mantenimiento',
            'ubicacion' => 'nullable|string|max:100',
        ]);

        $validated['activo'] = true;

        Mesa::create($validated);

        return redirect()->back()->with('success', 'Mesa registrada exitosamente.');
    }

    // Actualiza la mesa seleccionada
    public function update(Request $request, $id)
    {
        $mesa = Mesa::findOrFail($id);

        $validated = $request->validate([
            'numero' => 'required|string|max:50|unique:mesas,numero,' . $id,
            'capacidad' => 'required|integer|min:1',
            'estado' => 'required|string|in:Disponible,Ocupada,Reservada,Mantenimiento',
            'ubicacion' => 'nullable|string|max:100',
            'activo' => 'boolean'
        ]);

        $validated['activo'] = $request->has('activo');

        $mesa->update($validated);

        return redirect()->back()->with('success', 'Mesa actualizada exitosamente.');
    }

    // Elimina físicamente la mesa
    public function destroy($id)
    {
        $mesa = Mesa::findOrFail($id);
        $mesa->delete();

        return redirect()->back()->with('success', 'Mesa eliminada exitosamente.');
    }
}
```

---

## 2. Explicación de la Lógica del Código

### 1. `store()` (Línea 17)
* **Validación:** Impone que el número de mesa sea único en la base de datos (`unique:mesas,numero`) para prevenir que existan duplicados que puedan causar confusiones en el servicio.

### 2. `update()` (Línea 33)
* Permite modificar los datos de la mesa.
* **Excepción de Clave Única:** En la regla de validación de unicidad (`'numero' => 'unique:mesas,numero,' . $id`), se excluye el ID de la mesa que se está editando actualmente. De este modo, si solo modificamos la capacidad de la mesa conservando su mismo número, la validación no fallará por coincidencia con el mismo registro.
