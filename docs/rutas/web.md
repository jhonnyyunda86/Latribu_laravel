# Rutas Web del Sistema (web.php)

**Ubicación:** `routes/web.php`

## Descripción
Este archivo define el ruteo de la aplicación. Las rutas se dividen en grupos protegidos por autenticación y clasificados según el rol de usuario para garantizar la seguridad.

## Rutas por Grupo de Acceso

### 1. Rutas Públicas (Sin Autenticación)
* `GET /`: Página de inicio del restaurante (`welcome.blade.php`) con chatbot.

### 2. Grupo de Inicio Común (Autenticados)
* `GET /dashboard`: Controlador de redirección automática hacia el inicio según el rol (`admin`, `mesero` o `cliente`).

### 3. Grupo de Administradores
* **Usuarios:** `GET/POST/DELETE /admin/usuarios` (Gestión de personal de trabajo).
* **Mesas:** `GET/POST/PUT/DELETE /admin/mesas` (Gestión física de salón).
* **Menú:** `GET/POST/PUT/DELETE /admin/menu` (Platillos, precios, imágenes).
* **Pedidos:** `GET/POST/DELETE /admin/pedidos` (Control total e historial de comandas).
* **Reservas:** `GET/POST/DELETE /admin/reservas` (Revisar y validar reservas).
* **Reportes:** `GET /admin/reportes` (Analítica de ventas e ingresos).
* **Inventario:** `GET/POST/DELETE /admin/inventario` y `POST /admin/inventario/movimiento/{id}` (Gestión de existencias).

### 4. Grupo de Meseros
* **Dashboard:** `GET /mesero/dashboard` (Grilla de estado de mesas).
* **Tomar Comanda:** `GET /mesero/menu` (Carta digital con panel de pedido).
* **Guardar Comanda:** `POST /mesero/pedidos` (Guarda comanda, resta stock y genera factura).
* **Control de Mesas:** `GET /mesero/mesas` (Detalle de consumos por mesa).
* **Cambiar Estado Mesa:** `PATCH /mesero/mesas/{id}/status` (Modal de cambio de estado).
* **Bandeja de Pedidos:** `GET /mesero/pedidos` (Monitor de despacho de comandas).
* **Despachar Pedido:** `PATCH /mesero/pedidos/{id}/status` (Marcar como entregado).
* **Bandeja de Reservas:** `GET /mesero/reservas` (Monitoreo de reservaciones).
* **Editar Reserva:** `PATCH /mesero/reservas/{id}/status` (Modal de edición de estado de reserva).

### 5. Grupo de Clientes
* **Dashboard:** `GET /cliente/dashboard` (Portal de inicio del cliente).
* **Ver Menú:** `GET /cliente/menu` (Carta interactiva para compras).
* **Pedir Domicilio:** `POST /cliente/pedidos` (Crea pedido a domicilio, resta stock y genera factura).
* **Facturas:** `GET /cliente/facturas` (Lista de comprobantes de pago emitidos).
* **Descarga de PDF:** `GET /cliente/facturas/{id}/pdf` (Descargar factura POS en PDF).
* **Reservas:** `GET /cliente/reservas` (Historial y formulario de reservas).
* **Solicitar Reserva:** `POST /cliente/reservas` (Agenda reserva y genera su factura por $0.00).
