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
                    <?php echo htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Invitado'); ?>
                </h3>
            </div>

            <div class="bloque-resumen-totales">
                <p class="etiqueta-pago">RESUMEN DE CARGA:</p>
                
                <div class="fila-pago-desglose">
                    <span>Total de Componentes:</span>
                    <span class="valor-resaltado-blanco">
                        <?php echo isset($_SESSION['carrito']) ? array_sum($_SESSION['carrito']) : 0; ?> uds
                    </span>
                </div>
                
                <div class="fila-pago-desglose fila-total-final">
                    <span>Total a Liquidar:</span>
                    <span class="precio-total-naranja">
                        $<?php echo number_format($total_pago ?? 0, 2); ?>
                    </span>
                </div>
            </div>

            <form action="index.php?action=finalizar_orden" method="POST" class="formulario-pago-tarjeta">
                
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

                <button type="submit" class="btn-carrito btn-checkout-final">
                    CONFIRMAR Y DESPACHAR PEDIDO
                </button>
            </form>
            
            <a href="index.php?action=ver_carrito" class="enlace-auth link-retorno-box">← Modificar componentes del Box</a>
        </div>
    </main>

    <script src="assets/js/carrito.js"></script>
</body>
</html>