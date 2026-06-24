<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IGNIT - Iniciar Sesión</title>
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body>

    <header>
        <h1>IGNIT PERFORMANCE</h1>
    </header>
    
    <main>
        <div class="contenedor-auth">
            <h2>Acceder</h2>
            
            <?php if (isset($error)): ?>
                <div class="alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="index.php?action=procesar_login" method="POST">
                <div class="grupo-formulario">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                <div class="grupo-formulario">
                    <label>Contraseña</label>
                    <input type="password" name="contrasenia" required>
                </div>
                <button type="submit" class="btn-carrito">Entrar</button>
            </form>
            <a href="index.php?action=registro" class="enlace-auth">¿No tienes cuenta? Regístrate aquí</a>
        </div>
    </main>

</body>
</html>