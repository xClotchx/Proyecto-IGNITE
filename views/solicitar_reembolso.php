<?php
// Aseguramos que las variables existan para evitar errores si acceden directamente
if (!isset($id_pedido) || !isset($productos_del_pedido)) {
    header('Location: index.php?action=mis_pedidos');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitar Reembolso - IGNIT</title>
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body>

    <!-- Incluye aquí tu header o nav si lo tienes en un archivo aparte -->
    
    <main class="contenedor-reembolso">
        <h1>Solicitar Reembolso</h1>
        <p>Completa los detalles para procesar tu solicitud de devolución del pedido #<?php echo htmlspecialchars($id_pedido); ?>.</p>

        <form action="index.php?action=procesar_solicitud_reembolso" method="POST" enctype="multipart/form-data" class="form-ignit">
            
            <!-- Campo oculto para identificar el pedido -->
            <input type="hidden" name="id_pedido" value="<?php echo htmlspecialchars($id_pedido); ?>">
            
            <div class="form-group">
                <label>Selecciona el producto a devolver:</label>
                <select name="id_producto" class="input-ignit" required>
                    <?php foreach ($productos_del_pedido as $prod): ?>
                        <option value="<?php echo $prod['id']; ?>"><?php echo htmlspecialchars($prod['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Motivo de devolución:</label>
                <select name="motivo" class="input-ignit" required>
                    <option value="dañado">Llegó dañado</option>
                    <option value="faltante">Piezas faltantes</option>
                    <option value="error_pedido">Lo pedí por error</option>
                    <option value="tarde">Llegó muy tarde</option>
                    <option value="no_necesito">Ya no lo necesito</option>
                    <option value="no_esperado">No era lo que esperaba</option>
                </select>
            </div>

            <div class="form-group">
                <label>Descripción detallada:</label>
                <textarea name="descripcion" class="input-ignit" rows="4" placeholder="Explica brevemente qué sucedió..." required></textarea>
            </div>

            <div class="form-group">
                <label>Adjuntar imagen de evidencia (Obligatorio):</label>
                <input type="file" name="evidencia" accept="image/*" class="input-file-ignit" required>
            </div>

            <button type="submit" class="btn-carrito btn-agregar">Enviar Solicitud</button>
            
            <a href="index.php?action=mis_pedidos" style="display:block; text-align:center; margin-top:15px; color:#ccc; text-decoration:none;">Cancelar y volver</a>
        </form>
    </main>

    <!-- Incluye aquí tu footer si lo tienes -->

</body>
</html>