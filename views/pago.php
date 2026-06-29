<?php
// Aseguramos que la sesión esté iniciada
if (session_status() === PHP_SESSION_NONE) { session_start(); }

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

$total_pago = 0;
$lista_detallada = [];

// --- LÓGICA DE TARIFA DE ENVÍO DINÁMICA ---
$pais_usuario = $_SESSION['usuario_pais'] ?? 'Panamá'; 
$tarifa_envio = 15.00; 

try {
    $pdo = new PDO("mysql:host=localhost;dbname=proyecto;charset=utf8", "Eadmin", "12345");
    $stmt = $pdo->prepare("SELECT tarifa FROM tarifas_envio WHERE pais = ?");
    $stmt->execute([$pais_usuario]);
    $resultado = $stmt->fetchColumn();
    if ($resultado !== false) {
        $tarifa_envio = (float)$resultado;
    }
} catch (PDOException $e) {
    // Si hay error en BD, se mantiene la tarifa por defecto
}

if (!empty($_SESSION['carrito'])) {
    require_once 'models/ProductoModel.php';
    $pModel = new ProductoModel();
    foreach ($_SESSION['carrito'] as $id => $cant) {
        $prod = $pModel->buscarPorId($id);
        if ($prod) {
            $subtotal = $prod['precio'] * $cant;
            $total_pago += $subtotal;
            $lista_detallada[] = [
                'id' => $id,
                'nombre' => $prod['nombre'],
                'precio' => $prod['precio'],
                'cantidad' => $cant,
                'subtotal' => $subtotal
            ];
        }
    }
}

$gran_total = $total_pago + $tarifa_envio;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IGNIT - Confirmar Orden</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body>

    <header class="header-pago-ignit">
        <h1>IGNIT PERFORMANCE</h1>
    </header>
    
    <main class="contenedor-pago-wrapper">
        <div class="contenedor-auth caja-verificacion-orden">
            <h2>Verificación de la Orden</h2>
            
            <div class="bloque-piloto-asignado">
                <p class="etiqueta-pago">PILOTO ASIGNADO:</p>
                <h3 class="nombre-piloto-confirmar">
                    <?php echo htmlspecialchars(($_SESSION['usuario_nombre'] ?? 'Invitado') . ' ' . ($_SESSION['usuario_apellido'] ?? '')); ?>
                </h3>
            </div>
            <div class="bloque-info-envio" style="margin: 20px 0; padding: 15px; background: #222; border-radius: 8px; border-left: 4px solid #e67e22;">
                <p class="etiqueta-pago" style="margin-bottom: 5px;">ESTO SERÁ ENVIADO Al PAIS:</p>
                <p style="color: #fff; font-weight: bold; font-size: 1.1em; margin: 0;">
                    <?php echo htmlspecialchars($_SESSION['usuario_pais'] ?? 'País no especificado'); ?>
                </p>
            </div>
            <div class="bloque-info-envio" style="margin: 20px 0; padding: 15px; background: #222; border-radius: 8px; border-left: 4px solid #e67e22;">
                <p class="etiqueta-pago" style="margin-bottom: 5px;">CON LA DIRECCION:</p>
                <p style="color: #fff; font-weight: bold; font-size: 1.1em; margin: 0;">
                    <?php echo htmlspecialchars($_SESSION['usuario_direccion'] ?? 'Dirección no especificada'); ?>
                </p>
            </div>

            <div class="bloque-lista-componentes" style="margin-bottom: 20px;">
                <p class="etiqueta-pago">COMPONENTES DEL BOX:</p>
                <?php foreach ($lista_detallada as $item): ?>
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #333;">
                        <div style="display: flex; align-items: center;">
                            <img src="<?php echo obtenerRutaImagen($item['id']); ?>" style="width: 45px; height: 45px; object-fit: cover; margin-right: 15px;">
                            <div>
                                <strong style="display: block;"><?php echo htmlspecialchars($item['nombre']); ?></strong>
                                <small>Cant: <?php echo $item['cantidad']; ?> | $<?php echo number_format($item['precio'], 2); ?> c/u</small>
                            </div>
                        </div>
                        <span class="valor-resaltado-blanco">$<?php echo number_format($item['subtotal'], 2); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="bloque-resumen-totales">
                <p class="etiqueta-pago">RESUMEN DE CARGA:</p>
                
                <div class="fila-pago-desglose">
                    <span>Subtotal Componentes:</span>
                    <span class="valor-resaltado-blanco">$<?php echo number_format($total_pago, 2); ?></span>
                </div>

                <div class="fila-pago-desglose">
                    <span>Envío a <?php echo htmlspecialchars($pais_usuario); ?>:</span>
                    <span class="valor-resaltado-blanco">$<?php echo number_format($tarifa_envio, 2); ?></span>
                </div>
                
                <div class="fila-pago-desglose fila-total-final">
                    <span>Total a Liquidar:</span>
                    <span class="precio-total-naranja">$<?php echo number_format($gran_total, 2); ?></span>
                </div>
            </div>

            <form id="formulario-pago-tarjeta" class="formulario-pago-tarjeta">
                <input type="hidden" name="tarifa_envio" value="<?php echo $tarifa_envio; ?>">
                <input type="hidden" name="total_final" value="<?php echo $gran_total; ?>">
                
                <p class="etiqueta-pago">MÉTODO DE PAGO:</p>

                <div class="grupo-campo-pago">
                    <label for="nombre_tarjeta">Nombre en la Tarjeta</label>
                    <input type="text" id="nombre_tarjeta" name="nombre_tarjeta" placeholder="Ej. Elias Cedeño" required autocomplete="cc-name">
                </div>

                <div class="grupo-campo-pago">
                    <label for="numero_tarjeta">Número de Tarjeta</label>
                    <div style="position: relative; display: flex; align-items: center; width: 100%;">
                        <input type="text" id="numero_tarjeta" name="numero_tarjeta" placeholder="0000 0000 0000 0000" maxlength="19" required autocomplete="cc-number" style="width: 100%; padding-right: 65px; box-sizing: border-box;">
                        <div id="marca-tarjeta" class="contenedor-icono-tarjeta">
                            <i class="fa-solid fa-credit-card" id="icono-franquicia-dinamico"></i>
                        </div>
                    </div>
                </div>

                <div class="fila-campos-dobles">
                    <div class="grupo-campo-pago">
                        <label for="fecha_vence">Vencimiento</label>
                        <input type="text" id="fecha_vence" name="fecha_vence" placeholder="MM/AA" maxlength="5" required autocomplete="cc-exp">
                    </div>
                    <div class="grupo-campo-pago">
                        <label for="cvv">CVV / CVC</label>
                        <input type="password" id="cvv" name="cvv" placeholder="123" maxlength="4" required autocomplete="cc-csc">
                    </div>
                </div>

                <button type="submit" class="btn-carrito btn-checkout-final">CONFIRMAR Y DESPACHAR</button>
            </form>

            <div id="alerta-pago" style="display:none; padding:15px; margin-top:20px; border-radius:5px;"></div>
            
            <a href="index.php?action=ver_carrito" class="enlace-auth link-retorno-box">← Modificar componentes del Box</a>
        </div>
    </main>
<script src="assets/js/carrito.js"></script>
    
</body>
</html>