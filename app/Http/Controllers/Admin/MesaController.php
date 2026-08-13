<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mesa;
use Illuminate\Http\Request;

class MesaController extends Controller
{
    public function index()
    {
        $mesas = Mesa::all();
        return view('admin.mesas', compact('mesas'));
    }

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

    public function destroy($id)
    {
        $mesa = Mesa::findOrFail($id);
        $mesa->delete();

        return redirect()->back()->with('success', 'Mesa eliminada exitosamente.');
    }
}
