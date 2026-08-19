# Explicación Detallada del Archivo de Rutas: web.php

**Ruta física:** `routes/web.php`

En Laravel, las rutas definen las puertas de entrada al sistema web. Este archivo asocia las direcciones URL que escribe el usuario o que invoca el navegador con un controlador y método específico en PHP, controlando los permisos mediante middlewares.

---

## 1. Código Fuente Completo

```php
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
    Route::get('/cliente/pedidos', [ClienteController::class, 'pedidos'])->name('cliente.pedidos');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
```

---

## 2. Explicación Detallada de Cada Ruta

### 1. Ruta de la Landing Page
```php
Route::get('/', function () { return view('welcome'); });
```
* **Método HTTP:** `GET`
* **Acción:** Retorna directamente la vista pública `welcome.blade.php`.
* **Para qué sirve:** Es la página de inicio que ve cualquier visitante. Contiene el carrusel del restaurante y el chatbot de asistencia.

### 2. Enrutador Dinámico de Dashboard
```php
Route::get('/dashboard', function () { ... })->middleware(['auth', 'verified'])->name('dashboard');
```
* **Método HTTP:** `GET`
* **Middlewares:** `auth` (debe estar logueado) y `verified` (correo verificado).
* **Para qué sirve:** Es la ruta a la que Laravel redirige tras el login. Compara el rol del usuario autenticado (`auth()->user()->role->name`) y redirige:
  * Si es `'Admin'` o `'Administrador'` -> redirige a la ruta `admin.dashboard`.
  * Si es `'Mesero'` -> redirige a `mesero.dashboard`.
  * En cualquier otro caso (por defecto, `'Cliente'`) -> redirige a `cliente.dashboard`.

---

### 3. Grupo Protegido (Administrador, Mesero, Cliente)
Todas las siguientes rutas se encuentran dentro del grupo con middleware `['auth', 'verified']`.

#### 📂 Rutas del Administrador (`/admin/...`)

* **Dashboard de Admin:**
  * `GET /admin/dashboard`: Muestra el panel con analíticas y métricas financieras de ventas.

* **CRUD de Menú (Platos y Categorías):**
  * `GET /admin/menu`: Llama a `MenuController@index`. Carga y lista todos los platos y categorías.
  * `POST /admin/menu`: Llama a `MenuController@store`. Guarda un nuevo plato y sube su foto al servidor.
  * `PUT /admin/menu/{id}`: Llama a `MenuController@update`. Modifica los datos de un plato (usa `{id}` como parámetro para saber cuál editar) y reemplaza su foto si se proporciona una nueva.
  * `DELETE /admin/menu/{id}`: Llama a `MenuController@destroy`. Elimina el producto e invalida/borra su foto del disco local.

* **CRUD de Mesas Físicas:**
  * `GET /admin/mesas`: Carga y lista las mesas en el panel de administrador.
  * `POST /admin/mesas`: Registra una nueva mesa física con su capacidad y estado inicial.
  * `PUT /admin/mesas/{id}`: Actualiza capacidad o fuerza el estado de la mesa seleccionada.
  * `DELETE /admin/mesas/{id}`: Elimina la mesa.

* **Gestión de Reservas:**
  * `GET /admin/reservas`: Lista de reservas de clientes.
  * `PATCH /admin/reservas/{id}/status`: Permite al administrador cambiar el estado de la reserva (`Pendiente`, `Confirmada`, `Cancelada`). Se usa `PATCH` porque es una modificación parcial de un solo atributo.
  * `DELETE /admin/reservas/{id}`: Cancela y elimina la reserva.

* **Gestión y Auditoría de Pedidos:**
  * `GET /admin/pedidos`: Lista general de pedidos tomados por meseros o solicitados por clientes.
  * `PATCH /admin/pedidos/{id}/status`: Actualiza el estado de cocina (`En Espera`, `Entregado`).
  * `DELETE /admin/pedidos/{id}`: Ejecuta la eliminación del pedido, aplicando la lógica en cascada controlada en PHP para borrar primero los detalles de factura y evitar fallos por restricción SQL.

* **CRUD de Personal de Trabajo (Usuarios y Roles):**
  * `GET /admin/usuarios`: Lista los empleados y sus roles asignados.
  * `POST /admin/usuarios`: Registra un nuevo empleado (mesero, admin, etc.) cifrando su contraseña.
  * `PUT /admin/usuarios/{id}`: Edita datos básicos o actualiza contraseña si se rellena.
  * `PATCH /admin/usuarios/{id}/toggle`: Alterna el estado activo/inactivo del usuario para denegar el acceso.
  * `DELETE /admin/usuarios/{id}`: Remueve al usuario (con protección para evitar que el admin actual se borre a sí mismo).

* **Reportes y Gráficos Financieros:**
  * `GET /admin/reportes`: Procesa e indexa ingresos, ticket promedio y categorías más vendidas.
  * `GET /admin/reportes/pdf`: Genera e inicia la descarga del balance del mes en PDF.

* **Gestión de Inventario y Existencias:**
  * `GET /admin/inventario`: Carga y lista insumos y bitácora de movimientos.
  * `POST /admin/inventario/producto`: Registra un producto de almacén con stock inicial.
  * `POST /admin/inventario/movimiento/{id}`: Registra un movimiento manual de stock (Entrada/Salida) actualizando existencias.
  * `DELETE /admin/inventario/producto/{id}`: Elimina el insumo.

---

#### 📂 Rutas del Mesero (`/mesero/...`)

* **Dashboard de Mesas:**
  * `GET /mesero/dashboard`: Monitor de mesas para ver cuáles están ocupadas o libres.
* **Tomar Pedido:**
  * `GET /mesero/menu`: Carta digital adaptada para agregar productos a la comanda física actual.
  * `POST /mesero/pedidos`: Guarda la comanda, descuenta stock de inventario y genera factura pendiente automáticamente.
* **Control de Salón:**
  * `GET /mesero/mesas`: Detalle de consumos en mesa.
  * `PATCH /mesero/mesas/{id}/status`: Actualiza el estatus de la mesa física (Ej: `'Cuenta'`).
* **Bandeja de Cocina:**
  * `GET /mesero/pedidos`: Lista de comandas divididas en bandejas según preparación.
  * `PATCH /mesero/pedidos/{id}/status`: Despacha y entrega el pedido al cliente.
* **Bandeja de Reservas:**
  * `GET /mesero/reservas`: Muestra reservas agendadas.
  * `PATCH /mesero/reservas/{id}/status`: Modifica el estatus de reservas.

---

#### 📂 Rutas del Cliente (`/cliente/...`)

* **Dashboard del Cliente:**
  * `GET /cliente/dashboard`: Carga estadísticas y línea de tiempo de reservas del cliente actual.
* **Facturas:**
  * `GET /cliente/facturas`: Listado de consumos históricos facturados a nombre del cliente.
  * `GET /cliente/facturas/{id}/pdf`: Descarga la factura POS (tirilla térmica de 80mm de ancho) en formato PDF.
* **Menú y Domicilio:**
  * `GET /cliente/menu`: Carta interactiva con carrito de compras para delivery.
  * `POST /cliente/pedidos`: Registra el pedido de tipo domicilio y genera la factura.
* **Agenda de Reservaciones:**
  * `GET /cliente/reservas`: Historial de reservas y selector de mesas.
  * `POST /cliente/reservas`: Envía la reserva y crea un pedido representativo por $0.00 de respaldo para cumplir con las relaciones.
* **Mis Pedidos:**
  * `GET /cliente/pedidos`: Consulta el estado de preparación en tiempo real de sus pedidos a domicilio.
