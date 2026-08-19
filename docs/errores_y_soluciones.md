# Bitácora de Errores y Soluciones (Troubleshooting)

Este documento registra los problemas comunes encontrados durante el desarrollo e integración del sistema del restaurante **La Tribu**, detallando sus causas y soluciones técnicas implementadas.

---

## 1. Error de Truncado de Datos en ENUM (SQLSTATE[01000])

### Error Típico
`SQLSTATE[01000]: Warning: 1265 Data truncated for column 'estado' at row 1...` o `Data truncated for column 'tipo_pedido'`.

### Causa
Ocurre porque el motor de base de datos MySQL (modo estricto) tiene columnas definidas como `ENUM` con opciones estrictamente en minúsculas (Ej: `pendiente`, `en_preparacion`, `mesa`), pero el código PHP/Laravel o los formularios del frontend envían valores con mayúscula inicial o espacios (Ej: `En Espera`, `Mesa`).

### Solución Implementada
Se añadieron **Mutadores y Accesores** Eloquent directamente en los modelos afectados (Ej: [`Pedido.php`](file:///c:/laragon/www/la_tribu/app/Models/Pedido.php) y [`Reserva.php`](file:///c:/laragon/www/la_tribu/app/Models/Reserva.php)):
* El **Mutador** (`setEstadoAttribute`) intercepta la asignación del atributo y convierte el texto a minúsculas (`str_lower()`) antes de guardarlo en base de datos.
* El **Accesor** (`getEstadoAttribute`) intercepta la lectura y transforma el valor plano de la base de datos a un formato visual limpio (Ej: de `'pendiente'` a `'En Espera'`).

---

## 2. Restricción de Clave Foránea al Eliminar Pedidos (SQLSTATE[23000])

### Error Típico
`SQLSTATE[23000]: Integrity constraint violation: 1451 Cannot delete or update a parent row: a foreign key constraint fails ('facturas', CONSTRAINT 'facturas_pedido_id_foreign' FOREIGN KEY ('pedido_id') REFERENCES 'pedidos')`

### Causa
Se intentaba eliminar un registro de `Pedido` desde el panel de administración, pero dicho pedido tenía asociada una `Factura` (y esta a su vez tenía `DetalleFactura`). La base de datos tiene configurada la relación con `ON DELETE RESTRICT`, bloqueando la operación de borrado para evitar registros huérfanos.

### Solución Implementada
Se ajustó el método `destroy()` en el controlador [`PedidoController.php`](file:///c:/laragon/www/la_tribu/app/Http/Controllers/Admin/PedidoController.php):
* Se realiza un borrado manual en cascada controlado desde PHP:
  1. Verifica la existencia de la factura asociada.
  2. Elimina primero el desglose de productos de la factura (`DetalleFactura::delete()`).
  3. Elimina la factura principal (`Factura::delete()`).
  4. Elimina el desglose del pedido (`DetallePedido::delete()`).
  5. Finalmente, elimina el registro principal del `Pedido`.

---

## 3. Sesión Activa Visible al Retroceder Navegador (bfcache)

### Problema
Después de que un usuario (administrador, mesero o cliente) hace clic en "Cerrar Sesión", al presionar el botón "Atrás" del navegador web se seguía visualizando el panel privado con los datos del usuario.

### Causa
Los navegadores modernos usan caché local rápida (**bfcache**). Al ir atrás, el navegador renderiza una captura estática guardada en la memoria caché local de la computadora sin enviar una petición HTTP al servidor, simulando falsamente que la sesión sigue abierta.

### Solución Implementada
Se creó y registró un Middleware global para el grupo web:
* **Middleware creado:** [`PreventBackHistory.php`](file:///c:/laragon/www/la_tribu/app/Http/Middleware/PreventBackHistory.php).
* **Función:** Inserta cabeceras HTTP de control de caché en cada respuesta:
  `Cache-Control: no-cache, no-store, max-age=0, must-revalidate`
  `Pragma: no-cache`
* **Registro:** Se registró en [`bootstrap/app.php`](file:///c:/laragon/www/la_tribu/bootstrap/app.php) dentro del grupo `web`. Esto obliga al navegador a consultar al servidor al ir atrás, detectando que la sesión ya no existe y redirigiendo al `/login`.

---

## 4. Imágenes del Menú Rotas o No Visibles

### Problema
Las imágenes cargadas por el administrador del menú no se mostraban en las vistas de los meseros o clientes.

### Causa
La ruta de imagen en el componente HTML utilizaba la directiva `storage/uploads/` o similares de Laravel Storage, mientras que el controlador del menú (`MenuController.php`) guardaba las fotos directamente en la carpeta pública `/uploads/` del servidor.

### Solución Implementada
Se unificó el consumo en las vistas: las imágenes se leen directamente utilizando la ruta absoluta guardada en la base de datos (Ej: `product.imagen` o `/uploads/nombre_imagen.png`), asegurando que se carguen correctamente tanto en el rol de mesero como en el del cliente.

---

## 5. Excepción de Clase No Encontrada (Class Not Found)

### Error Típico
`Class "App\Http\Controllers\Reserva" not found`

### Causa
Se intenta invocar estáticamente una clase de modelo (como `Reserva::with()`) dentro de un controlador, pero el archivo del controlador no tiene el bloque de importación `use App\Models\Reserva;` en la sección superior de namespaces.

### Solución
Asegurar siempre la importación del namespace del modelo correspondiente en las líneas iniciales de cada controlador.
