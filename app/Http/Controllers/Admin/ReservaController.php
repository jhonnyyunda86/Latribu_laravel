<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reserva;
use Illuminate\Http\Request;

class ReservaController extends Controller
{
    public function index()
    {
        $reservas = Reserva::with(['user', 'mesa'])->get();
        return view('admin.reservas', compact('reservas'));
    }

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

    public function destroy($id)
    {
        $reserva = Reserva::findOrFail($id);
        $reserva->delete();

        return redirect()->back()->with('success', 'Reserva eliminada exitosamente.');
    }
}
