<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IGNIT - Catálogo de Partes</title>
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body>

    <header>
        <h1>IGNIT: Performance & Tuning</h1>
    </header>

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
                        <button class="btn-carrito">Añadir al Carrito</button>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No hay datos en el modelo.</p>
        <?php endif; ?>
    </main>

</body>
</html>