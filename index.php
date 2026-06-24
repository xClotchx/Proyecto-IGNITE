<?php
// 1. IMPORTANTE: Arrancar la sesión en la primera línea
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Importar controladores y modelos requeridos para el flujo Maestro-Detalle
require_once 'controllers/ProductosController.php';
require_once 'controllers/AuthController.php';
require_once 'models/ProductoModel.php';
require_once 'models/PedidoModel.php';

// 3. Capturar la acción que viene por la URL (si no viene nada, por defecto es 'catalogo')
$action = $_GET['action'] ?? 'catalogo';

// 4. Instanciar los controladores y modelos necesarios
$prodController = new ProductosController();
$authController = new AuthController();
$productoModel  = new ProductoModel();
$pedidoModel    = new PedidoModel();

// 5. El enrutador: decide qué archivo o método ejecutar
switch ($action) {
    case 'login':
        require_once 'views/login.php';
        break;

    case 'procesar_login':
        $authController->iniciarSesion();
        break;

    case 'registro':
        require_once 'views/registro.php';
        break;

    case 'procesar_registro':
        $authController->registrarUsuario();
        break;

    case 'logout':
        $authController->cerrarSesion();
        break;

    // Ruta AJAX que procesa la inserción al carrito sin recargar página
    case 'agregar_carrito':
        $id_producto = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if ($id_producto > 0) {
            if (!isset($_SESSION['carrito'])) {
                $_SESSION['carrito'] = [];
            }

            if (isset($_SESSION['carrito'][$id_producto])) {
                $_SESSION['carrito'][$id_producto]++; 
            } else {
                $_SESSION['carrito'][$id_producto] = 1; 
            }

            $cantidadTotal = array_sum($_SESSION['carrito']);

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'cantidadTotal' => $cantidadTotal
            ]);
        } else {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'ID de producto no válido'
            ]);
        }
        exit(); 
        break;

    // ==========================================================================
    // SECCIÓN: ACCIONES DEL CARRITO DE COMPRAS
    // ==========================================================================
    case 'ver_carrito':
        $prodController->mostrarCarrito();
        break;

    case 'actualizar_cantidad':
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $operacion = $_GET['operacion'] ?? '';

        if ($id > 0 && isset($_SESSION['carrito'][$id])) {
            if ($operacion === 'sumar') {
                $_SESSION['carrito'][$id]++;
            } elseif ($operacion === 'restar') {
                $_SESSION['carrito'][$id]--;
                if ($_SESSION['carrito'][$id] <= 0) {
                    unset($_SESSION['carrito'][$id]);
                }
            }
        }
        header('Location: index.php?action=ver_carrito');
        exit();
        break;

    case 'eliminar_carrito':
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if ($id > 0 && isset($_SESSION['carrito'][$id])) {
            unset($_SESSION['carrito'][$id]);
        }
        header('Location: index.php?action=ver_carrito');
        exit();
        break;

    // ==========================================================================
    // SECCIÓN: PROCESAMIENTO DE PAGO (CHECKOUT)
    // ==========================================================================
    case 'procesar_pago':
        // Barrera de seguridad: si no hay sesión iniciada, obligar a ir al login
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        // Si el carrito está vacío, no hay nada que pagar, mandamos al catálogo
        if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
            header('Location: index.php?action=catalogo');
            exit();
        }

        // Calcular el total definitivo desde la base de datos para inyectar a la vista
        $total_pago = 0;
        foreach ($_SESSION['carrito'] as $id_producto => $cantidad) {
            $prod = $productoModel->buscarPorId($id_producto);
            if ($prod) {
                $total_pago += ($prod['precio'] * $cantidad);
            }
        }

        // Cargamos la vista de confirmación inyectándole la variable $total_pago
        require_once 'views/pago.php';
        break;

    case 'finalizar_orden':
        // Validación de seguridad estricta para evitar accesos directos por URL
        if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
            header('Location: index.php?action=catalogo');
            exit();
        }

        // Recibir los datos de la pasarela mediante el método POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre_tarjeta = $_POST['nombre_tarjeta'] ?? '';
            $numero_tarjeta = $_POST['numero_tarjeta'] ?? '';
            $fecha_vence    = $_POST['fecha_vence'] ?? '';
            $cvv            = $_POST['cvv'] ?? '';
        }

        // Calcular el monto total acumulado consultando precios reales en la BD
        $total_pago = 0;
        foreach ($_SESSION['carrito'] as $id_producto => $cantidad) {
            $prod = $productoModel->buscarPorId($id_producto);
            if ($prod) {
                $total_pago += ($prod['precio'] * $cantidad);
            }
        }

        try {
            $id_usuario = $_SESSION['usuario_id'];
            $carrito = $_SESSION['carrito'];

            // GUARDAR EN BD: Registramos de golpe las dos tablas ('Pedidos' y 'Detalle_Pedido')
            $id_pedido_generado = $pedidoModel->registrarPedidoCompleto($id_usuario, $total_pago, $carrito, $productoModel);

            // Vaciamos el carrito de la sesión de forma limpia tras la inserción exitosa
            unset($_SESSION['carrito']);

            // Redirigimos directamente al sistema de rastreo pasando el ID autoincrementable por parámetro URL
            header("Location: index.php?action=rastrear_pedido&id=" . $id_pedido_generado);
            exit();

        } catch (Exception $e) {
            echo "<h3 style='color: #ff5f00; font-family: sans-serif; text-align: center; margin-top: 50px;'>
                    Error crítico al procesar el despacho en IGNIT PERFORMANCE: " . htmlspecialchars($e->getMessage()) . "
                  </h3>";
            exit();
        }
        break;

    // ==========================================================================
    // SECCIÓN: HISTORIAL Y ESTADO DE PEDIDOS
    // ==========================================================================
    case 'mis_pedidos':
        $prodController->mostrarMisPedidos();
        break;

    // ==========================================================================
    // SECCIÓN: SISTEMA DE TELEMETRÍA Y RASTREO
    // ==========================================================================
    case 'rastrear_pedido':
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        // Capturar el ID del pedido desde los parámetros de la URL
        $id_pedido = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $pedido = $pedidoModel->obtenerPorId($id_pedido);

        // Control de seguridad: Validar existencia y propiedad del pedido
        if (!$pedido || $pedido['id_usuario'] != $_SESSION['usuario_id']) {
            echo "<h3 style='color: #ef4c3c; font-family: sans-serif; text-align: center; margin-top: 50px;'>
                    Pedido no encontrado o acceso no autorizado a la telemetría.
                  </h3>";
            exit();
        }

        // Mapear variables dinámicas para alimentar la vista de la línea de tiempo
        $codigo_rastreo = "IG-ORD-" . $pedido['id_pedido'];
        $estado_actual  = $pedido['estado_pedido']; // Traerá 'pendiente', 'preparando', 'en_camino', 'ultima_milla' o 'entregado'
        $total_pedido   = $pedido['total'];

        require_once 'views/rastreo.php';
        break;

    case 'catalogo':
    default:
        $prodController->mostrarCatalogo();
        break;
}