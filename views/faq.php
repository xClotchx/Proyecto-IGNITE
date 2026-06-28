<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="assets/css/estilos.css">
    <title>Preguntas Frecuentes - IGNIT Performance</title>
</head>
<body>
    <main class="contenedor-faq">
        <a href="index.php" class="enlace-volver">← Volver al catálogo</a>
        <h1>Preguntas Frecuentes</h1>

        <?php 
        $preguntas = [
            ["¿Cómo realizo el seguimiento de mi pedido?", "Puedes ir a la sección 'Mis Pedidos' en tu cuenta para ver el estado en tiempo real."],
            ["¿Qué métodos de pago aceptan?", "Aceptamos tarjetas de crédito Visa y Mastercard de forma segura."],
            ["¿Tienen garantía en las piezas?", "Sí, todos nuestros componentes cuentan con garantía de fábrica contra defectos de fabricación."],
            ["¿Puedo cancelar mi pedido después de comprar?", "Sí, siempre y cuando el pedido no haya sido despachado del almacén."],
            ["¿Realizan envíos internacionales?", "Actualmente operamos a ciertos Paises, estamos expandiéndonos pronto."],
            ["¿Cómo sé si una pieza es compatible con mi vehículo?", "En cada ficha de producto incluimos una lista de modelos compatibles."],
            ["¿Qué pasa si recibo una pieza dañada?", "Por favor contacta a soporte dentro de las 24 horas tras recibir el paquete con fotos del daño."],
            ["¿Cuánto tarda en llegar mi pedido?", "Los envíos nacionales suelen tardar entre 2 y 5 días hábiles."],
            ["¿Puedo cambiar una pieza si no es lo que esperaba?", "Sí, aceptamos devoluciones en su empaque original dentro de los primeros 15 días."],
            ["¿Ofrecen asesoría técnica?", "Sí, nuestro equipo experto está disponible vía email para resolver dudas de instalación."],
            ["¿Cómo recupero mi contraseña?", "Usa el enlace '¿Olvidaste tu contraseña?' en la página de login."],
            ["¿Es seguro comprar en IGNIT?", "Totalmente, utilizamos certificados SSL para cifrar todos tus datos bancarios."],
            ["¿Tienen tienda física?", "Por el momento operamos exclusivamente de forma online."],
            ["¿Cómo puedo facturar mi compra?", "Al finalizar el pedido puedes ingresar tus datos fiscales."],
            ["¿Puedo añadir productos a un pedido ya confirmado?", "No, deberás realizar un pedido nuevo por separado."],
            ["¿Qué hago si mi pago es rechazado?", "Verifica que tu tarjeta tenga compras internacionales activas o contacta a tu banco."],
            ["¿Cómo puedo trabajar con ustedes?", "Envíanos tu portafolio a nuestra dirección de contacto oficial."],
            ["¿Las piezas vienen con manual de instalación?", "La mayoría sí, si no es así, podemos enviarte guías digitales."],
            ["¿Cómo puedo suscribirme a sus ofertas?", "Puedes crear una cuenta para recibir nuestro newsletter."],
            ["¿Qué sucede si un producto no tiene stock?", "Puedes usar el botón de 'Notificarme' para recibir un correo cuando vuelva a estar disponible."]
        ];

        foreach ($preguntas as $item): ?>
            <div class="faq-item">
                <h3><?php echo $item[0]; ?></h3>
                <p><?php echo $item[1]; ?></p>
            </div>
        <?php endforeach; ?>
    </main>
</body>
</html>