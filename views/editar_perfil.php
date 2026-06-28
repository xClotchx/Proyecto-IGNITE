<?php
// Protegemos la vista: si $usuario no existe, definimos un array vacío para evitar errores
if (!isset($usuario)) {
    $usuario = [
        'nombre' => '', 
        'apellido' => '', 
        'direccion' => '', 
        'telefono' => '', 
        'pais' => 'Panamá'
    ];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IGNIT - Editar Perfil</title>
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body>

    <header>
        <h1>IGNIT PERFORMANCE</h1>
    </header>
    
    <main>
        <div class="contenedor-auth">
            <h2>Editar Perfil de Piloto</h2>
            
            <form action="index.php?action=procesar_edicion" method="POST">
                <div class="grupo-formulario">
                    <label>Nombre</label>
                    <input type="text" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre'] ?? ''); ?>" required>
                </div>
                
                <div class="grupo-formulario">
                    <label>Apellido</label>
                    <input type="text" name="apellido" value="<?php echo htmlspecialchars($usuario['apellido'] ?? ''); ?>" required>
                </div>

                <div class="grupo-formulario">
                    <label>País de Residencia</label>
                    <select name="pais" required>
                        <?php 
                        $paises = ['Panamá', 'Costa Rica', 'Colombia', 'México', 'España', 'Argentina', 'Chile', 'Perú', 'Estados Unidos', 'Canadá', 'Brasil', 'Francia', 'Alemania', 'Italia', 'Japón'];
                        // Usamos ?? 'Panamá' como seguridad extra
                        $paisActual = $usuario['pais'] ?? 'Panamá';
                        foreach ($paises as $p) {
                            $selected = ($paisActual === $p) ? 'selected' : '';
                            echo "<option value='$p' $selected>$p</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="grupo-formulario">
                    <label>Dirección de Entrega</label>
                    <input type="text" name="direccion" value="<?php echo htmlspecialchars($usuario['direccion'] ?? ''); ?>" required>
                </div>

                <div class="grupo-formulario">
                    <label>Teléfono</label>
                    <input type="text" name="telefono" value="<?php echo htmlspecialchars($usuario['telefono'] ?? ''); ?>" required>
                </div>

                <button type="submit" class="btn-carrito">Guardar Cambios</button>
                <br>
                <br>
                <a href="index.php?action=logout" class="btn-logout">Cerrar Sesión</a>
            </form>
            <center>
            <a href="index.php" class="enlace-auth">← Volver al inicio</a>
            </center>
        </div>
    </main>

</body>
</html>