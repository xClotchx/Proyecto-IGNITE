
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IGNIT - Mis Pedidos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body>

    <header class="header-pago-ignit">
        <h1>IGNIT PERFORMANCE</h1>
    </header>

    <main class="contenedor-pago-wrapper contenedor-pedidos-historial">
        <div class="contenedor-auth caja-historial-ordenes">
            
            <div class="encabezado-historial">
                <h2>Historial de Órdenes</h2>
                <a href="index.php?action=catalogo" class="enlace-auth link-retorno-box">← Volver al Catálogo</a>
            </div>

            <?php if (empty($pedidos)): ?>
                <div class="historial-vacio">
                    <i class="fa-solid fa-folder-open"></i>
                    <p>Aún no has realizado ningún pedido de rendimiento en tu cuenta.</p>
                </div>
            <?php else: ?>
                <div class="tabla-responsiva-wrapper">
                    <table class="tabla-pedidos-ignit">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Fecha</th>
                                <th>Total</th>
                                <th>Estado actual</th>
                                <th class="columna-centrada">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $traduccion_estados = [
                                'pendiente'    => ['texto' => 'Pendiente', 'color' => '#ff5f00'],
                                'preparando'   => ['texto' => 'En Taller', 'color' => '#3498db'],
                                'en_camino'    => ['texto' => 'En Camino', 'color' => '#f1c40f'],
                                'ultima_milla' => ['texto' => 'Última Milla', 'color' => '#9b59b6'],
                                'entregado'    => ['texto' => 'Entregado', 'color' => '#2ecc71']
                            ];

                            foreach ($pedidos as $p): 
                                $est = $traduccion_estados[$p['estado_pedido']] ?? ['texto' => $p['estado_pedido'], 'color' => '#ffffff'];
                            ?>
                                <tr>
                                    <td class="codigo-pedido-resaltado">
                                        IG-ORD-<?php echo $p['id_pedido']; ?>
                                    </td>
                                    <td class="fecha-pedido-texto">
                                        <?php echo date('d/m/Y g:i A', strtotime($p['fecha_pedido'])); ?>
                                    </td>
                                    <td class="total-pedido-precio">
                                        $<?php echo number_format($p['total'], 2); ?>
                                    </td>
                                    <td>
                                        <span class="badge-estado-dinamico" style="--color-estado: <?php echo $est['color']; ?>;">
                                            <span class="punto-estado"></span>
                                            <?php echo $est['texto']; ?>
                                        </span>
                                    </td>
                                    <td class="columna-centrada">
                                        <div style="display: flex; gap: 5px; justify-content: center;">
                                            <!-- Botón Rastrear -->
                                            <a href="index.php?action=rastrear_pedido&id=<?php echo $p['id_pedido']; ?>" class="btn-carrito btn-tabla-rastreo">
                                                <i class="fa-solid fa-satellite-dish"></i> Rastrear
                                            </a>
                                            
                                            <!-- Botón Reembolso (Solo visible si el estado es 'entregado') -->
                                            <?php if ($p['estado_pedido'] === 'entregado'): ?>
                                                <a href="index.php?action=solicitar_reembolso&id=<?php echo $p['id_pedido']; ?>" class="btn-carrito btn-tabla-rastreo">
                                                    Reembolso
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>