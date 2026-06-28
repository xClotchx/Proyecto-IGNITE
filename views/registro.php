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
                    <label>País de Residencia</label>
                    <select name="pais" required style="width: 100%; padding: 10px; background: #333; color: white; border: 1px solid #444; border-radius: 4px; margin-top: 5px;">
                        <option value="Panamá" selected>Panamá</option>
                        <option value="Costa Rica">Costa Rica</option>
                        <option value="Colombia">Colombia</option>
                        <option value="México">México</option>
                        <option value="España">España</option>
                        <option value="Argentina">Argentina</option>
                        <option value="Chile">Chile</option>
                        <option value="Perú">Perú</option>
                        <option value="Estados Unidos">Estados Unidos</option>
                        <option value="Canadá">Canadá</option>
                        <option value="Brasil">Brasil</option>
                        <option value="Francia">Francia</option>
                        <option value="Alemania">Alemania</option>
                        <option value="Italia">Italia</option>
                        <option value="Japón">Japón</option>
                    </select>
                </div>
                <div class="grupo-formulario">
                    <label>Dirección de Entrega</label>
                    <input type="text" name="direccion" placeholder="Calle, número, ciudad" required>
                </div>
                <div class="grupo-formulario">
                    <label>Teléfono </label>
                    <input type="text" name="telefono" required>
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
            background: '#1e1e1e',
            color: '#f5f5f7',
            showCancelButton: true,
            confirmButtonColor: '#e67e22',
            cancelButtonColor: '#333333',
            confirmButtonText: 'Ir al Login',
            cancelButtonText: 'Corregir Datos',
            heightAuto: false
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'index.php?action=login';
            }
        });
    </script>
    <?php endif; ?>

</body>
</html>