<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\MesaController;
use App\Http\Controllers\Admin\ReservaController;
use App\Http\Controllers\Admin\PedidoController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReporteController;
use App\Http\Controllers\Admin\InventarioController;
use App\Http\Controllers\MeseroController;
use App\Http\Controllers\ClienteController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $role = auth()->user()->role?->name;

    if (in_array($role, ['Admin', 'Administrador'])) {
        return redirect()->route('admin.dashboard');
    }

    if ($role === 'Mesero') {
        return redirect()->route('mesero.dashboard');
    }

    return redirect()->route('cliente.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::get('/admin/menu', [MenuController::class, 'index'])->name('admin.menu');
    Route::post('/admin/menu', [MenuController::class, 'store'])->name('admin.menu.store');
    Route::put('/admin/menu/{id}', [MenuController::class, 'update'])->name('admin.menu.update');
    Route::delete('/admin/menu/{id}', [MenuController::class, 'destroy'])->name('admin.menu.destroy');

    Route::get('/admin/mesas', [MesaController::class, 'index'])->name('admin.mesas');
    Route::post('/admin/mesas', [MesaController::class, 'store'])->name('admin.mesas.store');
    Route::put('/admin/mesas/{id}', [MesaController::class, 'update'])->name('admin.mesas.update');
    Route::delete('/admin/mesas/{id}', [MesaController::class, 'destroy'])->name('admin.mesas.destroy');

    Route::get('/admin/reservas', [ReservaController::class, 'index'])->name('admin.reservas');
    Route::patch('/admin/reservas/{id}/status', [ReservaController::class, 'updateStatus'])->name('admin.reservas.status');
    Route::delete('/admin/reservas/{id}', [ReservaController::class, 'destroy'])->name('admin.reservas.destroy');

    Route::get('/admin/pedidos', [PedidoController::class, 'index'])->name('admin.pedidos');
    Route::patch('/admin/pedidos/{id}/status', [PedidoController::class, 'updateStatus'])->name('admin.pedidos.status');
    Route::delete('/admin/pedidos/{id}', [PedidoController::class, 'destroy'])->name('admin.pedidos.destroy');

    Route::get('/admin/usuarios', [UserController::class, 'index'])->name('admin.usuarios');
    Route::post('/admin/usuarios', [UserController::class, 'store'])->name('admin.usuarios.store');
    Route::put('/admin/usuarios/{id}', [UserController::class, 'update'])->name('admin.usuarios.update');
    Route::patch('/admin/usuarios/{id}/toggle', [UserController::class, 'toggleActive'])->name('admin.usuarios.toggle');
    Route::delete('/admin/usuarios/{id}', [UserController::class, 'destroy'])->name('admin.usuarios.destroy');

    Route::get('/admin/reportes', [ReporteController::class, 'index'])->name('admin.reportes');
    Route::get('/admin/reportes/pdf', [ReporteController::class, 'exportPdf'])->name('admin.reportes.pdf');

    Route::get('/admin/inventario', [InventarioController::class, 'index'])->name('admin.inventario');
    Route::post('/admin/inventario/producto', [InventarioController::class, 'storeProduct'])->name('admin.inventario.product.store');
    Route::post('/admin/inventario/movimiento/{id}', [InventarioController::class, 'registrarMovimiento'])->name('admin.inventario.movimiento.store');
    Route::delete('/admin/inventario/producto/{id}', [InventarioController::class, 'destroyProduct'])->name('admin.inventario.product.destroy');

    Route::get('/mesero/dashboard', [MeseroController::class, 'dashboard'])->name('mesero.dashboard');

    Route::get('/mesero/menu', [MeseroController::class, 'menu'])->name('mesero.menu');
    Route::post('/mesero/pedidos', [MeseroController::class, 'storePedido'])->name('mesero.pedidos.store');
    Route::get('/mesero/mesas', [MeseroController::class, 'mesas'])->name('mesero.mesas');
    Route::patch('/mesero/mesas/{id}/status', [MeseroController::class, 'updateMesaStatus'])->name('mesero.mesas.status');
    Route::get('/mesero/pedidos', [MeseroController::class, 'pedidos'])->name('mesero.pedidos');
    Route::patch('/mesero/pedidos/{id}/status', [MeseroController::class, 'updatePedidoStatus'])->name('mesero.pedidos.status');
    Route::get('/mesero/reservas', [MeseroController::class, 'reservas'])->name('mesero.reservas');
    Route::patch('/mesero/reservas/{id}/status', [MeseroController::class, 'updateReservaStatus'])->name('mesero.reservas.status');

    Route::get('/cliente/dashboard', [ClienteController::class, 'dashboard'])->name('cliente.dashboard');
    Route::get('/cliente/facturas', [ClienteController::class, 'facturas'])->name('cliente.facturas');
    Route::get('/cliente/menu', [ClienteController::class, 'menu'])->name('cliente.menu');
    Route::post('/cliente/pedidos', [ClienteController::class, 'storePedido'])->name('cliente.pedidos.store');
    Route::get('/cliente/facturas/{id}/pdf', [ClienteController::class, 'descargarPdf'])->name('cliente.facturas.pdf');
    Route::get('/cliente/reservas', [ClienteController::class, 'reservas'])->name('cliente.reservas');
    Route::post('/cliente/reservas', [ClienteController::class, 'storeReserva'])->name('cliente.reservas.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
