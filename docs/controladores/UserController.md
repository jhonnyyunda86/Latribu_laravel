# Documentación del Controlador: UserController

**Ruta física:** `app/Http/Controllers/Admin/UserController.php`

El `UserController` administra el registro, actualización de información y privilegios de acceso del personal de trabajo (meseros, cajeros, administradores) y clientes registrados en el sistema.

---

## 1. Código Fuente Completo

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    // Carga los usuarios y roles disponibles
    public function index()
    {
        $usuarios = User::with('role')->get();
        $roles = Role::all();
        return view('admin.usuarios', compact('usuarios', 'roles'));
    }

    // Registra un nuevo miembro del equipo o cliente
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rol_id' => 'required|exists:roles,id',
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:50',
            'password' => ['required', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($request->password);
        $validated['active'] = true;

        User::create($validated);

        return redirect()->back()->with('success', 'Usuario registrado exitosamente.');
    }

    // Modifica los datos de un usuario o sus contraseñas
    public function update(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        $validated = $request->validate([
            'rol_id' => 'required|exists:roles,id',
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:50',
            'password' => ['nullable', Rules\Password::defaults()],
            'active' => 'boolean'
        ]);

        // Cifra contraseña si se proporciona una nueva
        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            // Conserva la contraseña actual eliminando el índice del array validado
            unset($validated['password']);
        }

        $validated['active'] = $request->has('active');

        $usuario->update($validated);

        return redirect()->back()->with('success', 'Usuario actualizado exitosamente.');
    }

    // Alterna de forma lógica el estado activo del usuario
    public function toggleActive($id)
    {
        $usuario = User::findOrFail($id);
        $usuario->update([
            'active' => !$usuario->active
        ]);

        $status = $usuario->active ? 'activado' : 'desactivado';
        return redirect()->back()->with('success', "Usuario {$status} exitosamente.");
    }

    // Elimina el usuario del sistema protegiendo al admin en sesión
    public function destroy($id)
    {
        $usuario = User::findOrFail($id);

        // Bloqueo de seguridad: Evita el autoborrado del administrador
        if (auth()->id() === $usuario->id) {
            return redirect()->back()->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $usuario->delete();

        return redirect()->back()->with('success', 'Usuario eliminado exitosamente.');
    }
}
```

---

## 2. Explicación de la Lógica del Código

### 1. `store()` (Línea 21)
* **Encriptación de Contraseñas:** El controlador utiliza la fachada `Hash::make()` para convertir la contraseña del usuario en un hash criptográfico unidireccional y seguro antes de guardarla en la base de datos.

### 2. `update()` (Línea 40)
* El campo de contraseña es opcional en la edición (`'password' => 'nullable'`). Si el campo viene vacío, el método remueve la clave del arreglo con `unset($validated['password'])` para que Eloquent no modifique la clave actual del usuario.

### 3. Medida de Seguridad en `destroy()` (Línea 78)
* Cuenta con una regla de control: `auth()->id() === $usuario->id`. Evita que el administrador logueado elimine su propia cuenta, previniendo bloquear el panel administrativo.
