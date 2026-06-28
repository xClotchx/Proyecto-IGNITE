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

    <header>
        <h1>IGNIT: Performance & Tuning</h1>
    </header>

    <div class="contenedor-busqueda-ignit">
        <div class="caja-buscador">
            <span class="icono-buscar">🔍</span>
            <input type="text" id="buscador-productos" placeholder="Buscar por componente o categoría..." autocomplete="off">
        </div>
    </div>

    <main class="grid-productos">
        <?php if(isset($productos) && count($productos) > 0): ?>
            <?php foreach($productos as $p): ?>
                <article class="card">
        <form action="index.php?action=agregar_carrito" method="POST">
            <input type="hidden" name="id_producto" value="<?php echo $p['id']; ?>">
            
            <a href="index.php?action=ver_detalle&id=<?php echo $p['id']; ?>" style="text-decoration: none; color: inherit; display: block;">
                <div class="contenedor-img">
                    <img src="assets/img/producto/<?php echo $p['id']; ?>.jpeg" alt="Producto IGNIT">
                </div>
                
                <div class="info-producto" style="padding: 15px;">
                    <h3><?php echo htmlspecialchars($p['nombre']); ?></h3>
                    <p class="precio" style="color: #e67e22; font-weight: bold; font-size: 1.2rem;">
                        $<?php echo number_format($p['precio'], 2); ?>
                    </p>
                </div>
            </a>
            
            <div class="compra-producto" style="padding: 0 15px 15px 15px;">
            <button type="button" class="btn-carrito btn-agregar btn-agregar-catalogo" data-id="<?php echo $p['id']; ?>">
        Añadir al Carrito
            </button>
            </div>
        </form>
    </article>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No hay productos disponibles actualmente.</p>
        <?php endif; ?>
    </main>
    <footer class="footer-ignit">
    <div class="footer-container">
        <div class="footer-col">
            <h3>IGNIT PERFORMANCE</h3>
            <p>Especialistas en componentes de alto rendimiento. Llevamos tu vehículo al siguiente nivel.</p>
        </div>

        <div class="footer-col">
            <h4>Enlaces Rápidos</h4>
            <ul>
                <li><a href="index.php">Catálogo</a></li>
                <li><a href="index.php?action=mis_pedidos">Mis Pedidos</a></li>
                <li><a href="index.php?action=ver_carrito">Carrito</a></li>
                <li><a href="index.php?action=nosotros">Nosotros</a></li>
                <li><a href="index.php?action=preguntas_frecuentes">FAQ</a></li>
                <li><a href="index.php?action=politica_reembolsos">Política de Reembolsos</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>Soporte</h4>
            <ul>
                <li>Email: ignitsoporte@ignitperformance.com</li>
                <li>Horario: 24 horas del dia</li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>Seguridad</h4>
            <p>Transacciones protegidas con encriptación SSL.</p>
            <div class="iconos-pago">
                <i class="fa-brands fa-cc-visa"></i>
                <i class="fa-brands fa-cc-mastercard"></i>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; <?php echo date("Y"); ?> IGNIT Performance. Todos los derechos reservados.</p>
    </div>
</footer>

    <script src="assets/js/carrito.js"></script>
</body>
</html>