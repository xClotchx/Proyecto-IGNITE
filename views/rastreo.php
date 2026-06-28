<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IGNIT - Rastrear Pedido</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body>

    <header class="header-pago-ignit">
        <h1>IGNIT PERFORMANCE</h1>
    </header>

    <main class="contenedor-pago-wrapper">
        <div class="contenedor-auth caja-verificacion-orden">
            <h2>Telemetría de Envío</h2>
            
            <div class="bloque-piloto-asignado">
                <p class="etiqueta-pago">CÓDIGO DE RASTREO:</p>
                <h3 class="nombre-piloto-confirmar" style="color: #ffffff; font-family: monospace; letter-spacing: 1px;">
                    <?php echo htmlspecialchars($codigo_rastreo ?? 'IG-ORD-0'); ?>
                </h3>
                <p class="etiqueta-pago" style="margin-top: 12px; font-size: 0.8rem;">
                    TOTAL LIQUIDADO: <span class="precio-total-naranja">$<?php echo number_format($total_pedido ?? 0, 2); ?></span>
                </p>
            </div>

            <div class="linea-tiempo-rastreo">
                
                <?php 
                // Mapa de los nuevos 5 estados profesionales
                $estados = [
                    'pendiente'    => ['icon' => 'fa-receipt', 'label' => 'Pedido Pendiente', 'desc' => 'Pago verificado en sistema'],
                    'preparando'   => ['icon' => 'fa-boxes-packing', 'label' => 'Preparando Pedido', 'desc' => 'Empaquetando componentes en taller'],
                    'en_camino'    => ['icon' => 'fa-truck-fast', 'label' => 'Pedido en Camino', 'desc' => 'Despachado al centro de distribución'],
                    'ultima_milla' => ['icon' => 'fa-map-location-dot', 'label' => 'Última Milla', 'desc' => 'El transportista va hacia tu ubicación'],
                    'entregado'    => ['icon' => 'fa-flag-checkered', 'label' => 'Pedido Entregado', 'desc' => 'Componentes listos para instalar']
                ];

                // Asegurar estado por defecto si falla la carga
                $estado_actual = $estado_actual ?? 'pendiente';
                $pasado = true; 

                foreach ($estados as $key => $info): 
                    $clase_estado = "";
                    if ($key === $estado_actual) {
                        $clase_estado = "activo";
                        $pasado = false; // A partir de aquí, los siguientes son futuros (grises)
                    } elseif ($pasado) {
                        $clase_estado = "completado";
                    }
                ?>
                    <div class="etapa-rastreo <?php echo $clase_estado; ?>">
                        <div class="punto-linea">
                            <i class="fa-solid <?php echo $info['icon']; ?>"></i>
                        </div>
                        <div class="info-etapa">
                            <p class="titulo-etapa"><?php echo $info['label']; ?></p>
                            <p class="descripcion-etapa-texto" style="margin: 2px 0 0 0; font-size: 0.75rem; color: #666;">
                                <?php echo $info['desc']; ?>
                            </p>
                            <?php if ($key === $estado_actual): ?>
                                <p class="subtitulo-etapa">Fase Actual</p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

            </div>
            <a href="index.php?action=mis_pedidos" class="btn-carrito btn-checkout-final" style="text-align: center; text-decoration: none; display: block; box-sizing: border-box;">
                Volver al Pedidos
            </a>
            <a href="index.php?action=catalogo" class="btn-carrito btn-checkout-final" style="text-align: center; text-decoration: none; display: block; box-sizing: border-box;">
                Volver al Catálogo
            </a>
        </div>
    </main>

</body>
</html>