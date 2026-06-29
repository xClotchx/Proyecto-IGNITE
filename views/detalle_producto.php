<?php
if (!isset($recomendados)) { $recomendados = []; }
/** @var array $producto */
if (!isset($producto)) {
    $producto = ['id' => 0, 'nombre' => 'Producto no encontrado', 'precio' => 0, 'descripcion' => '', 'stock' => 0];
}

// Función auxiliar para obtener la imagen correcta
function obtenerRutaImagen($id) {
    $ruta_base = "assets/img/producto/" . $id;
    $extensiones = ['jpeg', 'jpg', 'png', 'webp', 'gif'];
    foreach ($extensiones as $ext) {
        if (file_exists($ruta_base . '.' . $ext)) {
            return $ruta_base . '.' . $ext;
        }
    }
    return "assets/img/default.png";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>IGNIT - <?php echo htmlspecialchars($producto['nombre']); ?></title>
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body>
    <nav class="navbar-ignit">
        <div class="logo-ignit">IGNIT PERFORMANCE</div>
        
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
                <a href="index.php?action=editar_perfil" class="usuario-logeado" style="text-decoration: none; color: inherit;">
                    <span style="border-bottom: 1px solid #e67e22; cursor: pointer;">
                        <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>
                    </span>
                </a>
                <a href="index.php?action=logout" class="btn-logout">Cerrar Sesión</a>
            <?php else: ?>
                <a href="index.php?action=login" class="link-login">Iniciar Sesión</a>
                <a href="index.php?action=registro" class="btn-registro-nav">Registrarse</a>
            <?php endif; ?>
        </div>
    </nav>

    <main class="contenedor-detalle">
        <a href="index.php" class="enlace-volver">← Volver al catálogo</a>
        <br>
        <br>
        <br>
        <div class="cuerpo-detalle">
            <div class="imagen-grande">
                <img src="<?php echo obtenerRutaImagen($producto['id']); ?>" alt="<?php echo $producto['nombre']; ?>">
            </div>
            
            <div class="info-completa">
                <h1><?php echo htmlspecialchars($producto['nombre']); ?></h1>
                <p class="precio-detalle">$<?php echo number_format($producto['precio'], 2); ?></p>
                <p><?php echo nl2br(htmlspecialchars($producto['descripcion'])); ?></p>
                <p><strong>Stock disponible:</strong> <?php echo $producto['stock']; ?></p>
                
                <div class="selector-cantidad-ignit">
                    <button type="button" class="btn-cant-control" onclick="ajustarCantidad(-1)">-</button>
                    <input type="number" id="cantidad" value="1" min="1" max="<?php echo $producto['stock']; ?>" readonly>
                    <button type="button" class="btn-cant-control" onclick="ajustarCantidad(1)">+</button>
                </div>
                
                <div id="contenedor-boton-accion">
                    <button type="button" class="btn-carrito btn-agregar" 
                            onclick="agregarAlCarritoConCantidad(<?php echo $producto['id']; ?>)">
                        Añadir al Carrito
                    </button>
                </div>
            </div>
        </div>

        <section class="contenedor-recomendados-wrapper">
            <h2 class="titulo-seccion">Productos Recomendados</h2>
            <div class="grid-productos">
                <?php if (!empty($recomendados)): ?>
                    <?php foreach($recomendados as $rec): ?>
                        <article class="card">
                            <a href="index.php?action=ver_detalle&id=<?php echo $rec['id_producto']; ?>">
                                <div class="contenedor-img">
                                    <img src="<?php echo obtenerRutaImagen($rec['id_producto']); ?>" alt="<?php echo htmlspecialchars($rec['nombre']); ?>">
                                </div>
                                <div class="info-card">
                                    <h3><?php echo htmlspecialchars($rec['nombre']); ?></h3>
                                    <p class="precio">$<?php echo number_format($rec['precio'], 2); ?></p>
                                </div>
                            </a>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No hay productos recomendados en este momento.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <script>
        function ajustarCantidad(cambio) {
            let input = document.getElementById('cantidad');
            let valor = parseInt(input.value) + cambio;
            let max = parseInt(input.getAttribute('max'));
            if (valor >= 1 && valor <= max) {
                input.value = valor;
            }
        }
    </script>
    <script src="assets/js/carrito.js"></script>
</body>
</html>