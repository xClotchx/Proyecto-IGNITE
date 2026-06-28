<?php
// Asegurar que las variables existan para el editor y para PHP
$items_factura = $items_factura ?? [];
$subtotal_calculado = $subtotal_calculado ?? 0;
$envio_factura = $envio_factura ?? 0;
$total_factura = $total_factura ?? 0;
$id_pedido = $id_pedido ?? 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; margin: 20px; }
        .header { border-bottom: 3px solid #f39c12; padding-bottom: 10px; margin-bottom: 20px; }
        .info-grid { display: table; width: 100%; margin-bottom: 20px; }
        .info-col { display: table-cell; width: 50%; vertical-align: top; }
        .info-box { background: #f9f9f9; padding: 15px; border-radius: 5px; border-left: 5px solid #2c3e50; margin: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #2c3e50; color: white; padding: 12px; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #ddd; }
        .total-box { margin-top: 30px; text-align: right; font-size: 20px; font-weight: bold; }
        .total-box span { color: #d35400; border: 2px solid #d35400; padding: 10px; display: block; margin-top: 10px;}
    </style>
</head>
<body>
    <div class="header">
        <h1>IGNIT Performance</h1>
        <p>Factura Electrónica</p>
    </div>
    
    <div>
        <strong>Código de rastreo de pedido:</strong><br>
        <span>IG-ORD-<?php echo htmlspecialchars($id_pedido ?? '0'); ?></span>
    </div>

    <div class="info-grid">
        <div class="info-col">
            <div class="info-box">
                <strong>Datos del Cliente:</strong><br>
                Nombre: <?php echo htmlspecialchars(($_SESSION['usuario_nombre'] ?? '') . ' ' . ($_SESSION['usuario_apellido'] ?? '')); ?><br>
                Teléfono: <?php echo htmlspecialchars($_SESSION['usuario_telefono'] ?? 'N/A'); ?>
            </div>
        </div>
        <div class="info-col">
            <div class="info-box">
                <strong>Detalles del Envío:</strong><br>
                País: <?php echo htmlspecialchars($_SESSION['usuario_pais'] ?? 'N/A'); ?><br>
                Dirección: <?php echo htmlspecialchars($_SESSION['usuario_direccion'] ?? 'N/A'); ?>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unit.</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items_factura as $item): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                <td><?php echo $item['cantidad']; ?></td>
                <td>$<?php echo number_format($item['precio'], 2); ?></td>
                <td>$<?php echo number_format($item['subtotal'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="total-box">
        <div>Subtotal Productos: $<?php echo number_format($subtotal_calculado, 2); ?></div>
        <div>Costo de envío: $<?php echo number_format($envio_factura, 2); ?></div>
        <span>TOTAL PAGADO: $<?php echo number_format($total_factura, 2); ?></span>
    </div>
</body>
</html>