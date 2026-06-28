<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IGNIT - Mi Carrito</title>
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body>

    <nav class="navbar-ignit">
        <div class="logo-ignit">
            <a href="index.php?action=catalogo" style="color: inherit; text-decoration: none;">IGNIT PERFORMANCE</a>
        </div>
        
        <div class="menu-auth">
            <a href="index.php?action=catalogo" class="link-login">Volver al Catálogo</a>
            
            <?php if (isset($_SESSION['usuario_nombre'])): ?>
                <span class="usuario-logeado">Piloto: <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></span>
                <a href="index.php?action=logout" class="btn-logout">Cerrar Sesión</a>
            <?php else: ?>
                <a href="index.php?action=login" class="link-login">Iniciar Sesión</a>
            <?php endif; ?>
        </div>
    </nav>

    <main class="contenedor-carrito-pagina">
        <h2>Tu Configuración de Partes</h2>

        <?php if (!empty($productos_carrito)): ?>
            <div class="layout-carrito">
                <div class="tabla-productos-wrapper">
                    <table class="tabla-carrito">
                        <thead>
                            <tr>
                                <th>Componente</th>
                                <th>Precio</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $total_general = 0;
                            foreach ($productos_carrito as $item): 
                                // Forzamos conversión a numérico para asegurar el cálculo
                                $precio = isset($item['precio']) ? (float)$item['precio'] : 0.00;
                                $cantidad = isset($item['cantidad']) ? (int)$item['cantidad'] : 0;
                                $subtotal = $precio * $cantidad;
                                $total_general += $subtotal;
                            ?>
                                <tr>
                                    <td class="td-producto">
                                        <div class="mini-img">
                                            <img src="assets/img/producto/<?php echo $item['id']; ?>.jpeg" 
                                                 onerror="this.src='assets/img/producto/default.jpeg'" 
                                                 alt="Componente">
                                        </div>
                                        <div>
                                            <span class="cat-tabla"><?php echo htmlspecialchars((string)($item['categoria'] ?? 'Sin categoría')); ?></span>
                                            <h4 class="nom-tabla"><?php echo htmlspecialchars((string)($item['nombre'] ?? 'Producto')); ?></h4>
                                        </div>
                                    </td>
                                    <td>$<?php echo number_format($precio, 2); ?></td>
                                    <td>
                                        <div class="control-cantidad">
                                            <a href="index.php?action=actualizar_cantidad&id=<?php echo $item['id']; ?>&operacion=restar" class="btn-cant">-</a>
                                            <span class="num-cant"><?php echo $cantidad; ?></span>
                                            <a href="index.php?action=actualizar_cantidad&id=<?php echo $item['id']; ?>&operacion=sumar" class="btn-cant">+</a>
                                        </div>
                                    </td>
                                    <td class="precio-subtotal">$<?php echo number_format($subtotal, 2); ?></td>
                                    <td>
                                        <a href="index.php?action=eliminar_carrito&id=<?php echo $item['id']; ?>" class="btn-eliminar-item">🗑️</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="resumen-pago">
                    <h3>Resumen del Pedido</h3>
                    <hr class="separador-ignit">
                    <div class="fila-resumen">
                        <span>Subtotal de Partes:</span>
                        <span>$<?php echo number_format($total_general, 2); ?></span>
                    </div>
                    <div class="fila-resumen total-resumen">
                        <span>Total Estimado:</span>
                        <span>$<?php echo number_format($total_general, 2); ?></span>
                    </div>

                    <?php if (isset($_SESSION['usuario_id'])): ?>
                        <a href="index.php?action=procesar_pago" class="btn-carrito btn-pagar">Proceder al Pago</a>
                    <?php else: ?>
                        <div class="alerta-checkout-invitado">
                            <p>Debes ingresar como piloto registrado para poder procesar la orden.</p>
                            <a href="index.php?action=login" class="btn-carrito">Iniciar Sesión para Pagar</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="carrito-vacio">
                <p>No tienes ningún componente asignado a tu box en este momento.</p>
                <a href="index.php?action=catalogo" class="btn-carrito">Ver Catálogo de Tuning</a>
            </div>
        <?php endif; ?>
    </main>

</body>
</html>