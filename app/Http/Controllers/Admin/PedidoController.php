<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    public function index()
    {
        $pedidos = Pedido::with(['user', 'mesa', 'mesero', 'detalles.producto'])->get();
        return view('admin.pedidos', compact('pedidos'));
    }

    public function updateStatus(Request $request, $id)
    {
        $pedido = Pedido::findOrFail($id);

        $request->validate([
            'estado' => 'required|string|in:En Espera,Entregado',
        ]);

        $pedido->update([
            'estado' => $request->estado
        ]);

        return redirect()->back()->with('success', 'Estado del pedido actualizado exitosamente.');
    }

    public function destroy($id)
    {
        $pedido = Pedido::findOrFail($id);
        $pedido->delete();

        return redirect()->back()->with('success', 'Pedido eliminado exitosamente.');
    }
}
