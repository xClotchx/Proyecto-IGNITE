<?php
// --- SEGURIDAD: Inicializar variable si no existe ---
if (!isset($datos_factura)) {
    $datos_factura = [
        'direccion'    => 'No especificada',
        'pais'         => 'No especificado', // Nuevo
        'tarifa_envio' => 0,                 // Nuevo
        'metodo_pago'  => 'No especificado',
        'items'        => [],
        'total_pago'   => 0
    ];
}
// Calculamos el subtotal de productos (sin envío)
$subtotal_productos = array_sum(array_column($datos_factura['items'], 'subtotal'));
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
                País de destino: <?php echo htmlspecialchars($datos_factura['pais'] ?? 'No especificado'); ?><br>
                Dirección de destino: <?php echo htmlspecialchars($datos_factura['direccion']); ?><br>
                Método de Pago: <?php echo htmlspecialchars($datos_factura['metodo_pago']); ?>
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
            <?php foreach ($datos_factura['items'] as $item): ?>
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
        <div>Subtotal Productos: $<?php echo number_format($subtotal_productos, 2); ?></div>
        <div>Costo de envío: $<?php echo number_format($datos_factura['tarifa_envio'], 2); ?></div>
        <span>TOTAL PAGADO: $<?php echo number_format($datos_factura['total_pago'], 2); ?></span>
    </div>
</body>
</html>