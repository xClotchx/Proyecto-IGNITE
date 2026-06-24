document.addEventListener('DOMContentLoaded', () => {
    const botonesAgregar = document.querySelectorAll('.btn-agregar');
    const iconoCarrito = document.getElementById('icono-carrito');
    const contadorCarrito = document.getElementById('contador-carrito');

    botonesAgregar.forEach(boton => {
        boton.addEventListener('click', (e) => {
            e.preventDefault();
            
            const productoId = boton.getAttribute('data-id');
            
            // 1. Calcular posiciones físicas de los elementos en pantalla
            const rectBoton = boton.getBoundingClientRect();
            const rectCarrito = iconoCarrito.getBoundingClientRect();

            // 2. Crear y posicionar la partícula en el botón de origen
            const particula = document.createElement('div');
            particula.classList.add('particula-voladora');
            
            particula.style.top = `${rectBoton.top + (rectBoton.height / 2) - 10}px`;
            particula.style.left = `${rectBoton.left + (rectBoton.width / 2) - 10}px`;
            
            document.body.appendChild(particula);

            // 3. Forzar el viaje de la partícula hacia el navbar
            setTimeout(() => {
                particula.style.top = `${rectCarrito.top + (rectCarrito.height / 2) - 10}px`;
                particula.style.left = `${rectCarrito.left + (rectCarrito.width / 2) - 10}px`;
                particula.style.transform = 'scale(0.2)'; 
                particula.style.opacity = '0';            
            }, 50);

            // 4. Limpieza del DOM y efecto elástico en la burbuja numérica
            setTimeout(() => {
                particula.remove();
                
                contadorCarrito.classList.add('badge-pop');
                setTimeout(() => contadorCarrito.classList.remove('badge-pop'), 200);

                // 5. Llamada asíncrona (AJAX) al controlador PHP
                actualizarCarritoBackend(productoId);

            }, 700);
        });
    });

    function actualizarCarritoBackend(id) {
        fetch(`index.php?action=agregar_carrito&id=${id}`, {
            method: 'GET'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Refresca el contador con el número total real que tiene la sesión
                contadorCarrito.textContent = data.cantidadTotal;
            }
        })
        .catch(error => console.error('Error de comunicación con IGNIT Backend:', error));
    }
});
// ==========================================================================
    // SISTEMA DE FILTRADO EN TIEMPO REAL PARA EL BUSCADOR
    // ==========================================================================
    const buscador = document.getElementById('buscador-productos');
    const tarjetasProductos = document.querySelectorAll('.grid-productos .card');

    if (buscador) {
        buscador.addEventListener('input', (e) => {
            // Pasamos el texto a minúsculas y quitamos espacios en blanco extraños
            const terminoBusqueda = e.target.value.toLowerCase().trim();

            tarjetasProductos.forEach(tarjeta => {
                // Capturamos el texto del nombre y de la categoría dentro de cada tarjeta
                const nombreProducto = tarjeta.querySelector('h3').textContent.toLowerCase();
                const categoriaProducto = tarjeta.querySelector('.categoria').textContent.toLowerCase();

                // Evaluamos si el término coincide con el nombre O con la categoría
                if (nombreProducto.includes(terminoBusqueda) || categoriaProducto.includes(terminoBusqueda)) {
                    // Si coincide, se muestra la tarjeta restableciendo su diseño flex/block
                    tarjeta.style.display = ""; 
                } else {
                    // Si no coincide, se oculta por completo del grid
                    tarjeta.style.display = "none";
                }
            });
        });
    }
  // ==========================================================================
// CONTROLADOR DE LOGOS DE TARJETAS (VERSION ESTABLE FONT AWESOME)
// ==========================================================================
document.addEventListener('DOMContentLoaded', () => {
    const inputTarjeta = document.getElementById('numero_tarjeta');
    const contenedorIcono = document.getElementById('marca-tarjeta');
    const iconoInterno = document.getElementById('icono-franquicia-dinamico');

    if (inputTarjeta && contenedorIcono && iconoInterno) {
        inputTarjeta.addEventListener('input', (e) => {
            let valorFila = e.target.value.replace(/\D/g, '');
            
            // Evaluamos el patrón del número para switchear las clases de Font Awesome
            if (valorFila.startsWith('4')) {
                // Cambia al logo oficial de Visa
                iconoInterno.className = 'fa-brands fa-cc-visa';
                contenedorIcono.className = 'contenedor-icono-tarjeta visa-activa';
            } else if (/^(5[1-5]|2[2-7])/.test(valorFila)) {
                // Cambia al logo oficial de Mastercard
                iconoInterno.className = 'fa-brands fa-cc-mastercard';
                contenedorIcono.className = 'contenedor-icono-tarjeta mastercard-activa';
            } else {
                // Retorna al icono neutral de tarjeta de crédito genérica
                iconoInterno.className = 'fa-solid fa-credit-card';
                contenedorIcono.className = 'contenedor-icono-tarjeta';
            }

            // Mantiene el formateador con espacios cada 4 dígitos
            let valorFormateado = valorFila.match(/.{1,4}/g);
            if (valorFormateado) {
                e.target.value = valorFormateado.join(' ');
            } else {
                e.target.value = '';
            }
        });
    }
});
// ==========================================================================
// MÁSCARA AUTOMÁTICA PARA EL CAMPO DE VENCIMIENTO (MM/AA)
// ==========================================================================
const inputVence = document.getElementById('fecha_vence');

if (inputVence) {
    // Escuchamos cuando el usuario escribe
    inputVence.addEventListener('input', (e) => {
        // 1. Limpiamos el valor para dejar solo números enteros
        let valor = e.target.value.replace(/\D/g, '');
        
        // 2. Si el usuario escribe los primeros números, formateamos
        if (valor.length > 2) {
            // Cortamos los primeros 2 dígitos para el mes y los siguientes para el año
            e.target.value = valor.substring(0, 2) + '/' + valor.substring(2, 4);
        } else {
            e.target.value = valor;
        }
    });

    // Escuchamos las teclas especiales (como borrar) para que no se tranque con el "/"
    inputVence.addEventListener('keydown', (e) => {
        let valor = e.target.value;
        
        // Si el usuario presiona borrar (Backspace) y justo el caracter anterior es el '/',
        // borramos el número del mes automáticamente para evitar que se quede trabado
        if (e.key === 'Backspace' && valor.length === 4) {
            e.preventDefault(); // Detenemos el borrado por defecto
            inputVence.value = valor.substring(0, 2); // Dejamos solo el mes escrito
        }
    });
}