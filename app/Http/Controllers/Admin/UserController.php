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
    public function index()
    {
        $usuarios = User::with('role')->get();
        $roles = Role::all();
        return view('admin.usuarios', compact('usuarios', 'roles'));
    }

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

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        $validated['active'] = $request->has('active');

        $usuario->update($validated);

        return redirect()->back()->with('success', 'Usuario actualizado exitosamente.');
    }

    public function toggleActive($id)
    {
        $usuario = User::findOrFail($id);
        $usuario->update([
            'active' => !$usuario->active
        ]);

        $status = $usuario->active ? 'activado' : 'desactivado';
        return redirect()->back()->with('success', "Usuario {$status} exitosamente.");
    }

    public function destroy($id)
    {
        $usuario = User::findOrFail($id);

        // Impedir que el admin actual se elimine a sí mismo
        if (auth()->id() === $usuario->id) {
            return redirect()->back()->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $usuario->delete();

        return redirect()->back()->with('success', 'Usuario eliminado exitosamente.');
    }
}
