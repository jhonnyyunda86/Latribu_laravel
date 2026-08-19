# Documentación del Controlador: ReservaController

**Ruta física:** `app/Http/Controllers/Admin/ReservaController.php`

El `ReservaController` permite a la administración supervisar la agenda completa de reservaciones de mesas del local, habilitando la confirmación y cancelación rápida.

---

## 1. Código Fuente Completo

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reserva;
use Illuminate\Http\Request;

class ReservaController extends Controller
{
    // Carga todas las reservas con relaciones de usuario y mesa
    public function index()
    {
        $reservas = Reserva::with(['user', 'mesa'])->get();
        return view('admin.reservas', compact('reservas'));
    }

    // Permite cambiar el estado de confirmación de la reserva
    public function updateStatus(Request $request, $id)
    {
        $reserva = Reserva::findOrFail($id);

        $request->validate([
            'estado' => 'required|string|in:Pendiente,Confirmada,Cancelada',
        ]);

        $reserva->update([
            'estado' => $request->estado
        ]);

        return redirect()->back()->with('success', 'Estado de la reserva actualizado exitosamente.');
    }

    // Elimina la reserva de la base de datos
    public function destroy($id)
    {
        $reserva = Reserva::findOrFail($id);
        $reserva->delete();

        return redirect()->back()->with('success', 'Reserva eliminada exitosamente.');
    }
}
```

---

## 2. Explicación de la Lógica del Código

### 1. `updateStatus()` (Línea 17)
* Cambia el estado de la reserva.
* **Transparencia en BD:** El controlador recibe valores con mayúscula inicial (`Confirmada`, `Cancelada`), los cuales son procesados de forma segura e invisible al formato exigido por la base de datos MySQL gracias al **Mutador** del modelo `Reserva`.
