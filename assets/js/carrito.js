// ==========================================================================
// 1. LÓGICA DE CARRITO (AGREGAR Y ACTUALIZAR)
// ==========================================================================
function agregarAlCarritoConCantidad(idProducto) {
    const cantidad = document.getElementById('cantidad').value;
    const btnPrincipal = document.querySelector('.btn-agregar');

    fetch('index.php?action=agregar_carrito', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: idProducto, cantidad: parseInt(cantidad) })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // 1. Feedback visual: Cambiar a "¡AGREGADO!"
            if (btnPrincipal) {
                btnPrincipal.textContent = '¡AGREGADO!';
                btnPrincipal.style.background = '#27ae60';
                
                // Regresa a "Actualizar Carrito" tras 2 segundos
                setTimeout(() => {
                    btnPrincipal.textContent = 'Actualizar Carrito';
                    btnPrincipal.style.background = '';
                }, 2000);
            }

            // 2. Actualizar contador del navbar
            const contador = document.getElementById('contador-carrito');
            if (contador) {
                contador.textContent = data.cantidadTotal;
                contador.classList.add('badge-pop');
                setTimeout(() => contador.classList.remove('badge-pop'), 200);
            }
        }
    })
    .catch(error => console.error('Error IGNIT:', error));
}

// Función que se dispara con los botones + y -
function ajustarCantidad(cambio) {
    let input = document.getElementById('cantidad');
    let valor = parseInt(input.value) + cambio;
    let max = parseInt(input.getAttribute('max'));
    
    if (valor >= 1 && valor <= max) {
        input.value = valor;
        
        // Al ajustar la cantidad, el usuario debe saber que el botón actualizará el carrito
        const btnPrincipal = document.querySelector('.btn-agregar');
        if (btnPrincipal) {
            btnPrincipal.textContent = 'Actualizar Carrito';
            btnPrincipal.style.background = ''; // Resetea color
            btnPrincipal.disabled = false;
        }
    }
}

// ==========================================================================
// 2. DOMContentLoaded: EVENTOS Y UI
// ==========================================================================
document.addEventListener('DOMContentLoaded', () => {
    
    // --- LÓGICA DE ANIMACIÓN DE PARTÍCULA (CATÁLOGO) ---
    const botonesAgregar = document.querySelectorAll('.btn-agregar-catalogo'); // Clase específica para el catálogo
    const iconoCarrito = document.getElementById('icono-carrito');
    const contadorCarrito = document.getElementById('contador-carrito');

    botonesAgregar.forEach(boton => {
        boton.addEventListener('click', (e) => {
            e.preventDefault();
            const productoId = boton.getAttribute('data-id');
            
            // Llamada GET tradicional para catálogo
            fetch(`index.php?action=agregar_carrito&id=${productoId}`)
                .then(res => res.json())
                .then(data => { 
                    if (data.success && contadorCarrito) {
                        contadorCarrito.textContent = data.cantidadTotal;
                        contadorCarrito.classList.add('badge-pop');
                        setTimeout(() => contadorCarrito.classList.remove('badge-pop'), 200);
                    }
                });
        });
    });

    // --- FILTRADO EN TIEMPO REAL ---
    const buscador = document.getElementById('buscador-productos');
    const tarjetasProductos = document.querySelectorAll('.grid-productos .card');

    if (buscador) {
        buscador.addEventListener('input', (e) => {
            const termino = e.target.value.toLowerCase().trim();
            tarjetasProductos.forEach(tarjeta => {
                const nombre = tarjeta.querySelector('h3').textContent.toLowerCase();
                tarjeta.style.display = (nombre.includes(termino)) ? "" : "none";
            });
        });
    }

    // --- MÁSCARA TARJETA (PAGOS) ---
    const inputTarjeta = document.getElementById('numero_tarjeta');
    if (inputTarjeta) {
        inputTarjeta.addEventListener('input', (e) => {
            let valor = e.target.value.replace(/\D/g, '');
            e.target.value = valor.match(/.{1,4}/g)?.join(' ') || '';
        });
    }

    // --- MÁSCARA FECHA VENCIMIENTO ---
    const inputVence = document.getElementById('fecha_vence');
    if (inputVence) {
        inputVence.addEventListener('input', (e) => {
            let val = e.target.value.replace(/\D/g, '');
            if (val.length > 2) e.target.value = val.substring(0, 2) + '/' + val.substring(2, 4);
            else e.target.value = val;
        });
    }
});