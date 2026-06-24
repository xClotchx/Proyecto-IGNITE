<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IGNIT - Crear Cuenta</title>
    <link rel="stylesheet" href="assets/css/estilos.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

    <header>
        <h1>IGNIT PERFORMANCE</h1>
    </header>
    
    <main>
        <div class="contenedor-auth">
            <h2>Registro de Piloto</h2>
            
            <?php if (isset($error)): ?>
                <div class="alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="index.php?action=procesar_registro" method="POST">
                <div class="grupo-formulario">
                    <label>Nombre</label>
                    <input type="text" name="nombre" required>
                </div>
                <div class="grupo-formulario">
                    <label>Apellido</label>
                    <input type="text" name="apellido" required>
                </div>
                <div class="grupo-formulario">
                    <label>Teléfono (Opcional)</label>
                    <input type="text" name="telefono">
                </div>
                <div class="grupo-formulario">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                <div class="grupo-formulario">
                    <label>Contraseña</label>
                    <input type="password" name="contrasenia" required>
                </div>
                <button type="submit" class="btn-carrito">Registrarme</button>
            </form>
            <a href="index.php?action=login" class="enlace-auth">¿Ya tienes cuenta? Inicia sesión</a>
        </div>
    </main>

    <?php if (isset($error_registro) && $error_registro === 'duplicado'): ?>
    <script>
        Swal.fire({
            title: '¡Correo ya registrado!',
            text: 'Este correo electrónico ya se encuentra vinculado a un piloto en nuestra plataforma.',
            icon: 'warning',
            background: '#1e1e1e', // Fondo gris carbón de tu estilo
            color: '#f5f5f7',      // Texto blanco satinado
            showCancelButton: true,
            confirmButtonColor: '#e67e22', // Ámbar deportivo de IGNIT
            cancelButtonColor: '#333333',  // Gris oscuro para cancelar
            confirmButtonText: 'Ir al Login',
            cancelButtonText: 'Corregir Datos',
            heightAuto: false
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirección limpia al controlador de Login
                window.location.href = 'index.php?action=login';
            }
        });
    </script>
    <?php endif; ?>

</body>
</html>