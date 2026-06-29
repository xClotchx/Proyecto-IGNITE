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
            if (btnPrincipal) {
                btnPrincipal.textContent = '¡AGREGADO!';
                btnPrincipal.style.background = '#27ae60';
                setTimeout(() => {
                    btnPrincipal.textContent = 'Actualizar Carrito';
                    btnPrincipal.style.background = '';
                }, 2000);
            }
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

function ajustarCantidad(cambio) {
    let input = document.getElementById('cantidad');
    let valor = parseInt(input.value) + cambio;
    let max = parseInt(input.getAttribute('max'));
    
    if (valor >= 1 && valor <= max) {
        input.value = valor;
        const btnPrincipal = document.querySelector('.btn-agregar');
        if (btnPrincipal) {
            btnPrincipal.textContent = 'Actualizar Carrito';
            btnPrincipal.style.background = '';
            btnPrincipal.disabled = false;
        }
    }
}

// ==========================================================================
// 2. DOMContentLoaded: EVENTOS Y UI
// ==========================================================================
document.addEventListener('DOMContentLoaded', () => {
    
    // --- LÓGICA DE ANIMACIÓN (CATÁLOGO) ---
    const botonesAgregar = document.querySelectorAll('.btn-agregar-catalogo');
    const contadorCarrito = document.getElementById('contador-carrito');

    botonesAgregar.forEach(boton => {
        boton.addEventListener('click', (e) => {
            e.preventDefault();
            const productoId = boton.getAttribute('data-id');
            
            boton.textContent = 'Añadiendo...';
            boton.style.opacity = '0.7';

            // CAMBIO: Petición corregida a POST con JSON
            fetch('index.php?action=agregar_carrito', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: productoId, cantidad: 1 })
            })
            .then(res => res.json())
            .then(data => { 
                if (data.success) {
                    boton.textContent = '¡Agregado!';
                    boton.style.opacity = '1';
                    setTimeout(() => boton.textContent = 'Añadir al Carrito', 2000);

                    if (contadorCarrito) {
                        contadorCarrito.textContent = data.cantidadTotal;
                        contadorCarrito.classList.add('badge-pop');
                        setTimeout(() => contadorCarrito.classList.remove('badge-pop'), 200);
                    }
                }
            })
            .catch(err => {
                console.error('Error al agregar:', err);
                boton.textContent = 'Error';
                setTimeout(() => boton.textContent = 'Añadir al Carrito', 2000);
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

    // --- MÁSCARAS Y DETECCIÓN DE TARJETA (PAGOS) ---
    const inputTarjeta = document.getElementById('numero_tarjeta');
    const iconoFranquicia = document.getElementById('icono-franquicia-dinamico');

    if (inputTarjeta) {
        inputTarjeta.addEventListener('input', (e) => {
            let valor = e.target.value.replace(/\D/g, '');
            e.target.value = valor.match(/.{1,4}/g)?.join(' ') || '';
            
            if (iconoFranquicia) {
                if (valor.startsWith('4')) {
                    iconoFranquicia.className = 'fa-brands fa-cc-visa';
                    iconoFranquicia.style.color = '#1a1f71';
                } else if (valor.startsWith('5')) {
                    iconoFranquicia.className = 'fa-brands fa-cc-mastercard';
                    iconoFranquicia.style.color = '#eb001b';
                } else {
                    iconoFranquicia.className = 'fa-solid fa-credit-card';
                    iconoFranquicia.style.color = '#fff';
                }
            }
        });
    }

    const inputVence = document.getElementById('fecha_vence');
    if (inputVence) {
        inputVence.addEventListener('input', (e) => {
            let val = e.target.value.replace(/\D/g, '');
            if (val.length > 2) e.target.value = val.substring(0, 2) + '/' + val.substring(2, 4);
            else e.target.value = val;
        });
    }

    // --- PROCESAMIENTO DE PAGO CON MANEJO DE ERRORES ---
    document.getElementById('formulario-pago-tarjeta').addEventListener('submit', function(e) {
        e.preventDefault();
        let btn = this.querySelector('button');
        let alerta = document.getElementById('alerta-pago');
        
        btn.disabled = true;
        btn.innerText = 'PROCESANDO...';
        alerta.style.display = 'none';
        
        fetch('index.php?action=finalizar_orden', {
            method: 'POST',
            body: new FormData(this)
        })
        .then(res => {
            if (!res.ok) throw new Error('Error de conexión con el servidor.');
            return res.json();
        })
        .then(data => {
            if(data.success) {
                window.location.href = data.redirect;
            } else {
                alerta.innerHTML = `
                    <div style="margin-bottom: 8px;"><i class="fa-solid fa-triangle-exclamation" style="color: #e67e22; font-size: 1.2em;"></i></div>
                    <span style="display:block; font-size: 0.8em; color: #aaa; text-transform: uppercase;">Error en el pago</span>
                    <span style="display:block; margin-top: 4px;">${data.mensaje.toUpperCase()}</span>
                `;
                alerta.style.display = 'block';
                setTimeout(() => { alerta.style.display = 'none'; }, 6000);
                btn.disabled = false;
                btn.innerText = 'CONFIRMAR Y DESPACHAR';
            }
        })
        .catch(err => {
            alerta.innerHTML = `<span style="display:block;">Error de conexión: Verifica tu red o intenta de nuevo.</span>`;
            alerta.style.display = 'block';
            btn.disabled = false;
            btn.innerText = 'CONFIRMAR Y DESPACHAR';
            console.error('Error:', err);
        });
    });
    
});