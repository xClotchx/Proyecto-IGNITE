<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IGNIT - Catálogo de Partes</title>
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
                
                <span class="usuario-logeado">
                    <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>
                </span>
                <a href="index.php?action=logout" class="btn-logout">Cerrar Sesión</a>
            <?php else: ?>
                <a href="index.php?action=login" class="link-login">Iniciar Sesión</a>
                <a href="index.php?action=registro" class="btn-registro-nav">Registrarse</a>
            <?php endif; ?>
        </div>
    </nav>

    <header>
        <h1>IGNIT: Performance & Tuning</h1>
    </header>

    <div class="contenedor-busqueda-ignit">
        <div class="caja-buscador">
            <span class="icono-buscar">🔍</span>
            <input type="text" id="buscador-productos" placeholder="Buscar por componente o categoría (ej. Intercooler, Suspensión...)" autocomplete="off">
        </div>
    </div>

    <main class="grid-productos">
        <?php if(isset($productos) && count($productos) > 0): ?>
            <?php foreach($productos as $p): ?>
                <article class="card">
                    
                    <div class="contenedor-img">
                        <img src="assets/img/producto/<?php echo $p['id']; ?>.jpeg" alt="Producto IGNIT">
                    </div>
                    
                    <div class="info-producto">
                        <span class="categoria"><?php echo htmlspecialchars($p['categoria']); ?></span>
                        <h3><?php echo htmlspecialchars($p['nombre']); ?></h3>
                        <p><?php echo htmlspecialchars($p['descripcion']); ?></p>
                    </div>
                    
                    <div class="compra-producto">
                        <p class="precio">$<?php echo number_format($p['precio'], 2); ?></p>
                        <p class="stock">Stock: <?php echo $p['stock']; ?> uds</p>
                        
                        <button type="button" class="btn-carrito btn-agregar" data-id="<?php echo $p['id']; ?>">
                            Añadir al Carrito
                        </button>
                        
                    </div>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No hay datos en el modelo.</p>
        <?php endif; ?>
    </main>

    <script src="assets/js/carrito.js"></script>
</body>
</html>