# Documentación del Controlador: ReporteController

**Ruta física:** `app/Http/Controllers/Admin/ReporteController.php`

El `ReporteController` consolida la información de ventas, productos populares y estadísticas financieras en rangos de fechas específicos, permitiendo exportar reportes ejecutivos en formato PDF.

---

## 1. Código Fuente Completo

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\Reserva;
use App\Models\Mesa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    // Genera el reporte financiero interactivo filtrado por fechas
    public function index(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', now()->endOfMonth()->format('Y-m-d'));

        // Estadísticas principales filtradas por fecha
        $ventasTotales = Pedido::where('estado', 'Entregado')
            ->whereBetween('created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
            ->sum('total');

        $pedidosCount = Pedido::whereBetween('created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])->count();

        $reservasCount = Reserva::whereBetween('created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])->count();

        $mesasCount = Mesa::count();

        // Ventas por categoría (usando Query Builder seguro)
        $ventasPorCategoria = DB::table('detalle_pedidos')
            ->join('productos', 'detalle_pedidos.producto_id', '=', 'productos.id')
            ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
            ->join('pedidos', 'detalle_pedidos.pedido_id', '=', 'pedidos.id')
            ->where('pedidos.estado', 'Entregado')
            ->whereBetween('pedidos.created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
            ->select('categorias.nombre as categoria', DB::raw('SUM(detalle_pedidos.subtotal) as total'))
            ->groupBy('categorias.id', 'categorias.nombre')
            ->get();

        // Productos más vendidos
        $productosMasVendidos = DB::table('detalle_pedidos')
            ->join('productos', 'detalle_pedidos.producto_id', '=', 'productos.id')
            ->join('pedidos', 'detalle_pedidos.pedido_id', '=', 'pedidos.id')
            ->where('pedidos.estado', 'Entregado')
            ->whereBetween('pedidos.created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
            ->select('productos.nombre', DB::raw('SUM(detalle_pedidos.cantidad) as cantidad'), DB::raw('SUM(detalle_pedidos.subtotal) as total'))
            ->groupBy('productos.id', 'productos.nombre')
            ->orderBy('cantidad', 'desc')
            ->limit(5)
            ->get();

        return view('admin.reportes', compact(
            'ventasTotales',
            'pedidosCount',
            'reservasCount',
            'mesasCount',
            'ventasPorCategoria',
            'productosMasVendidos',
            'fechaInicio',
            'fechaFin'
        ));
    }

    // Exporta el reporte financiero filtrado a formato PDF
    public function exportPdf(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', now()->endOfMonth()->format('Y-m-d'));

        $ventasTotales = Pedido::where('estado', 'Entregado')
            ->whereBetween('created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
            ->sum('total');

        $pedidosCount = Pedido::whereBetween('created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])->count();
        $reservasCount = Reserva::whereBetween('created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])->count();
        $mesasCount = Mesa::count();

        // Ventas por categoría (usando Query Builder seguro)
        $ventasPorCategoria = DB::table('detalle_pedidos')
            ->join('productos', 'detalle_pedidos.producto_id', '=', 'productos.id')
            ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
            ->join('pedidos', 'detalle_pedidos.pedido_id', '=', 'pedidos.id')
            ->where('pedidos.estado', 'Entregado')
            ->whereBetween('pedidos.created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
            ->select('categorias.nombre as categoria', DB::raw('SUM(detalle_pedidos.subtotal) as total'))
            ->groupBy('categorias.id', 'categorias.nombre')
            ->get();

        // Productos más vendidos
        $productosMasVendidos = DB::table('detalle_pedidos')
            ->join('productos', 'detalle_pedidos.producto_id', '=', 'productos.id')
            ->join('pedidos', 'detalle_pedidos.pedido_id', '=', 'pedidos.id')
            ->where('pedidos.estado', 'Entregado')
            ->whereBetween('pedidos.created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
            ->select('productos.nombre', DB::raw('SUM(detalle_pedidos.cantidad) as cantidad'), DB::raw('SUM(detalle_pedidos.subtotal) as total'))
            ->groupBy('productos.id', 'productos.nombre')
            ->orderBy('cantidad', 'desc')
            ->limit(10)
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reportes-pdf', compact(
            'ventasTotales',
            'pedidosCount',
            'reservasCount',
            'mesasCount',
            'ventasPorCategoria',
            'productosMasVendidos',
            'fechaInicio',
            'fechaFin'
        ));

        return $pdf->download("reporte-tribu-{$fechaInicio}-a-{$fechaFin}.pdf");
    }
}
```

---

## 2. Explicación de la Lógica del Código

### 1. Rango de Fechas por Defecto (Líneas 16-17)
* Si no se especifican filtros en la solicitud HTTP, el controlador calcula dinámicamente el inicio del mes (`now()->startOfMonth()`) y el fin de mes actual (`now()->endOfMonth()`) para presentar estadísticas relevantes de inmediato.

### 2. Consultas Complejas con Query Builder (Línea 31)
* Para optimizar la velocidad de carga de datos financieros, el controlador utiliza `DB::table` y uniones (`join`) seguras para consolidar y sumar los subtotales de compras agrupadas por clasificaciones en una sola consulta de base de datos (`groupBy`), previniendo sobrecargar la memoria de la aplicación Laravel.
