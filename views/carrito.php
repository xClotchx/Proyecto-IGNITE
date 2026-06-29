
<?php
// Función auxiliar para obtener la imagen correcta
function obtenerRutaImagen($id) {
    $ruta_base = "assets/img/producto/" . $id;
    $extensiones = ['jpeg', 'jpg', 'png', 'webp', 'gif'];
    foreach ($extensiones as $ext) {
        if (file_exists($ruta_base . '.' . $ext)) {
            return $ruta_base . '.' . $ext;
        }
    }
    return "assets/img/default.png"; // Asegúrate de tener esta imagen de fallback
}
?>
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
    <div class="logo-ignit">IGNIT PERFORMANCE</div>

    <div class="nav-links-center">
        <a href="index.php?action=nosotros">Nosotros</a>
        <a href="index.php?action=preguntas_frecuentes">FAQ</a>
        <a href="index.php?action=politica_reembolsos">Política de Reembolsos</a>
    </div>
    
    <div class="menu-auth">
        <a href="index.php?action=ver_carrito" class="enlace-carrito-nav">
            <div class="contenedor-carrito-nav" id="icono-carrito">
                <span class="icono-bolsa">🛒</span>
                <span id="contador-carrito" class="badge-carrito">
                    <?php echo isset($_SESSION['carrito']) ? array_sum($_SESSION['carrito']) : 0; ?>
                </span>
            </div>
        </a>

        <?php if (isset($_SESSION['usuario_nombre'])): ?>
            <a href="index.php?action=mis_pedidos" class="link-mis-pedidos">📦 Mis Pedidos</a>
            <a href="index.php?action=editar_perfil" class="usuario-logeado">
                <span class="nombre-usuario-activo"><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></span>
            </a>
        <?php else: ?>
            <a href="index.php?action=login" class="link-login">Iniciar Sesión</a>
            <a href="index.php?action=registro" class="btn-registro-nav">Registrarse</a>
        <?php endif; ?>
    </div>
</nav>

    <main class="contenedor-carrito-pagina">
        <a href="index.php" class="enlace-volver">← Volver al catálogo</a>
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
                                $precio = isset($item['precio']) ? (float)$item['precio'] : 0.00;
                                $cantidad = isset($item['cantidad']) ? (int)$item['cantidad'] : 0;
                                $subtotal = $precio * $cantidad;
                                $total_general += $subtotal;
                            ?>
                                <tr>
                                    <td class="td-producto">
                                        <div class="mini-img">
                                            <!-- Aplicación de la lógica de imagen dinámica -->
                                            <img src="<?php echo obtenerRutaImagen($item['id']); ?>" alt="Componente">
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