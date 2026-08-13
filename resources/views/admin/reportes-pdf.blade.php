<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Ventas - La Tribu</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #121619;
            margin: 0;
            padding: 10px;
            font-size: 11px;
            line-height: 1.5;
        }
        .header {
            border-bottom: 2px solid #d4af37;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .logo-title {
            font-size: 22px;
            font-weight: bold;
            color: #121619;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin: 0;
        }
        .subtitle {
            font-size: 11px;
            color: #d4af37;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 1px;
            margin-top: 3px;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .meta-table td {
            padding: 2px 0;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #121619;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
            margin-top: 25px;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .metrics-grid {
            width: 100%;
            margin-bottom: 20px;
            border-spacing: 10px;
            margin-left: -10px;
            margin-right: -10px;
        }
        .metric-card {
            background-color: #fdfbf7;
            border: 1px solid #e5e7eb;
            padding: 12px;
            text-align: center;
            border-radius: 8px;
            width: 25%;
        }
        .metric-title {
            font-size: 8px;
            color: #6b7280;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 4px;
        }
        .metric-val {
            font-size: 15px;
            font-weight: bold;
            color: #121619;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .data-table th {
            background-color: #121619;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
            padding: 7px 10px;
            text-align: left;
        }
        .data-table td {
            padding: 7px 10px;
            border-bottom: 1px solid #f3f4f6;
        }
        .data-table tr:nth-child(even) td {
            background-color: #fdfbf7;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            background-color: #d4af37;
            color: #121619;
            padding: 2px 6px;
            font-weight: bold;
            border-radius: 4px;
            font-size: 8px;
            text-transform: uppercase;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 8px;
        }
    </style>
</head>
<body>

    <!-- Cabecera -->
    <table class="header" style="width: 100%;">
        <tr>
            <td>
                <h1 class="logo-title">La Tribu</h1>
                <div class="subtitle">Reporte Ejecutivo de Rendimiento</div>
            </td>
            <td style="text-align: right; vertical-align: bottom; font-size: 10px; color: #6b7280;">
                Generado el {{ now()->format('d/m/Y H:i A') }}
            </td>
        </tr>
    </table>

    <!-- Rango de Fechas -->
    <table class="meta-table">
        <tr>
            <td style="width: 50%;">
                <strong>Rango de Consulta:</strong> 
                {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}
            </td>
            <td style="width: 50%; text-align: right;">
                <strong>Moneda:</strong> COP ($)
            </td>
        </tr>
    </table>

    <!-- Indicadores Clave -->
    <h3 class="section-title">Resumen de Indicadores</h3>
    <table class="metrics-grid">
        <tr>
            <td class="metric-card">
                <div class="metric-title">Ingresos Totales</div>
                <div class="metric-val" style="color: #10b981;">${{ number_format($ventasTotales, 2) }}</div>
            </td>
            <td class="metric-card">
                <div class="metric-title">Pedidos Completados</div>
                <div class="metric-val">{{ $pedidosCount }}</div>
            </td>
            <td class="metric-card">
                <div class="metric-title">Reservas Registradas</div>
                <div class="metric-val" style="color: #d4af37;">{{ $reservasCount }}</div>
            </td>
            <td class="metric-card">
                <div class="metric-title">Mesas Totales</div>
                <div class="metric-val">{{ $mesasCount }}</div>
            </td>
        </tr>
    </table>

    <!-- Bloque de Dos Secciones (Ventas por Categoría y Platillos más vendidos) -->
    <h3 class="section-title">Ingresos por Categorías de Comida</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>Categoría</th>
                <th class="text-right">Total Recaudado</th>
                <th class="text-right">Participación (%)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $granTotalCategorias = $ventasPorCategoria->sum('total');
            @endphp
            @forelse($ventasPorCategoria as $c)
                @php
                    $porcentaje = $granTotalCategorias > 0 ? ($c->total / $granTotalCategorias) * 100 : 0;
                @endphp
                <tr>
                    <td style="font-weight: bold;">{{ $c->categoria }}</td>
                    <td class="text-right">${{ number_format($c->total, 2) }}</td>
                    <td class="text-right">{{ number_format($porcentaje, 1) }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center" style="color: #9ca3af;">No se registran ventas por categoría en este rango.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h3 class="section-title">Top 10 Platillos Más Vendidos (Favoritos del Público)</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 8%;">Posición</th>
                <th>Nombre del Platillo</th>
                <th class="text-center" style="width: 15%;">Unidades Vendidas</th>
                <th class="text-right" style="width: 25%;">Total Recaudado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($productosMasVendidos as $index => $p)
                <tr>
                    <td class="text-center"><span class="badge">#{{ $index + 1 }}</span></td>
                    <td style="font-weight: bold;">{{ $p->nombre }}</td>
                    <td class="text-center">{{ $p->cantidad }} uds</td>
                    <td class="text-right" style="font-weight: bold;">${{ number_format($p->total, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center" style="color: #9ca3af;">No se registran unidades vendidas en este rango.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pie de Página -->
    <div class="footer">
        La Tribu Restaurante © {{ date('Y') }} - Este reporte es de carácter interno y confidencial.
    </div>

</body>
</html>
