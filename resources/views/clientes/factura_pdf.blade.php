<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura {{ $factura->numero_factura }}</title>
    <style>
        @page {
            margin: 8px;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 10px;
            color: #000;
            line-height: 1.2;
            margin: 0;
            padding: 0;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .bold {
            font-weight: bold;
        }
        .header {
            margin-bottom: 8px;
        }
        .header h2 {
            font-size: 16px;
            margin: 0;
            letter-spacing: 1px;
        }
        .header p {
            margin: 2px 0;
            font-size: 8px;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }
        .info-table, .items-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 1px 0;
            font-size: 8px;
        }
        .items-table th {
            border-bottom: 1px dashed #000;
            text-align: left;
            font-size: 8px;
            padding: 2px 0;
        }
        .items-table td {
            padding: 3px 0;
            font-size: 8px;
            vertical-align: top;
        }
        .totals-table {
            width: 100%;
            margin-top: 5px;
        }
        .totals-table td {
            padding: 1.5px 0;
            font-size: 9px;
        }
        .footer {
            margin-top: 15px;
            font-size: 8px;
        }
    </style>
</head>
<body>

    <div class="header text-center">
        <h2>LA TRIBU</h2>
        <p class="bold">Familia & Sabor</p>
        <p>NIT: 901.234.567-8</p>
        <p>Dirección: Av. Principal No. 12-34</p>
        <p>Teléfono: +57 300 123 4567</p>
        <p>Medellín, Colombia</p>
    </div>

    <div class="divider"></div>

    <table class="info-table">
        <tr>
            <td class="bold">Factura:</td>
            <td class="text-right">{{ $factura->numero_factura }}</td>
        </tr>
        <tr>
            <td class="bold">Fecha:</td>
            <td class="text-right">{{ $factura->created_at->format('d/m/Y h:i A') }}</td>
        </tr>
        <tr>
            <td class="bold">Cliente:</td>
            <td class="text-right">{{ $factura->pedido?->user?->name ?? 'Cliente General' }}</td>
        </tr>
        <tr>
            <td class="bold">Servicio:</td>
            <td class="text-right">
                @if($factura->pedido?->tipo_pedido === 'Mesa')
                    Mesa {{ $factura->pedido?->mesa?->numero ?? 'General' }}
                @else
                    Domicilio
                @endif
            </td>
        </tr>
        <tr>
            <td class="bold">Med. Pago:</td>
            <td class="text-right">{{ ucfirst($factura->metodo_pago) }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 55%;">Cant x Producto</th>
                <th style="width: 20%;" class="text-right">Unit</th>
                <th style="width: 25%;" class="text-right">Subt</th>
            </tr>
        </thead>
        <tbody>
            @foreach($factura->detalles as $item)
                <tr>
                    <td>
                        {{ $item->cantidad }} x {{ $item->nombre_producto }}
                    </td>
                    <td class="text-right font-mono">${{ number_format($item->precio_unitario, 0, ',', '.') }}</td>
                    <td class="text-right font-mono">${{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    <table class="totals-table">
        <tr>
            <td>Subtotal:</td>
            <td class="text-right font-mono">${{ number_format($factura->subtotal, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Impuesto (IVA 0%):</td>
            <td class="text-right font-mono">${{ number_format($factura->impuesto, 0, ',', '.') }}</td>
        </tr>
        <tr class="bold">
            <td style="font-size: 11px;">TOTAL:</td>
            <td class="text-right font-mono" style="font-size: 11px;">${{ number_format($factura->total, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    <div class="footer text-center">
        <p class="bold">¡Gracias por su visita!</p>
        <p>Conserve su ticket de compra</p>
        <p>La Tribu Club VIP - www.latribucolombia.com</p>
    </div>

</body>
</html>
