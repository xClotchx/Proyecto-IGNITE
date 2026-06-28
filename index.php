<?php
// 1. IMPORTANTE: Arrancar la sesión en la primera línea
session_start();

// Activar visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Carga de dependencias
require_once 'vendor/autoload.php';
require_once 'controllers/ProductosController.php';
require_once 'controllers/AuthController.php';
require_once 'models/ProductoModel.php';
require_once 'models/PedidoModel.php';

// 3. Capturar la acción
$action = $_GET['action'] ?? 'catalogo';

// 4. Instanciar controladores y modelos
$prodController = new ProductosController();
$authController = new AuthController();
$productoModel  = new ProductoModel();
$pedidoModel    = new PedidoModel();

// 5. El enrutador
switch ($action) {
    case 'login': require_once 'views/login.php'; break;
    case 'procesar_login': $authController->iniciarSesion(); break;
    case 'registro': require_once 'views/registro.php'; break;
    case 'procesar_registro': $authController->registrarUsuario(); break;
    case 'logout': $authController->cerrarSesion(); break;
    case 'politica_reembolsos': require_once 'views/reembolsos.php'; break;
    case 'editar_perfil': $authController->editarPerfil(); break;
    case 'procesar_edicion': $authController->procesarEdicion(); break;
    case 'preguntas_frecuentes': require_once 'views/faq.php'; break;
    case 'nosotros': require_once 'views/nosotros.php'; break;

    case 'solicitar_reembolso':
        if (!isset($_SESSION['usuario_id'])) { header('Location: index.php?action=login'); exit(); }
        $id_pedido = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $pedido = $pedidoModel->obtenerPorId($id_pedido);
        if (!$pedido || (isset($pedido['id_usuario']) && $pedido['id_usuario'] != $_SESSION['usuario_id'])) {
            die("Acceso no autorizado.");
        }
        $productos_del_pedido = $pedidoModel->obtenerProductosPorPedido($id_pedido);
        require_once 'views/solicitar_reembolso.php';
        break;

    case 'procesar_solicitud_reembolso':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['evidencia'])) {
            $upload_dir = 'uploads/reembolsos/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $file_name = 'reb_' . $_POST['id_pedido'] . '_' . time() . '_' . basename($_FILES['evidencia']['name']);
            move_uploaded_file($_FILES['evidencia']['tmp_name'], $upload_dir . $file_name);
            echo "<script>alert('Solicitud enviada correctamente.'); window.location='index.php?action=mis_pedidos';</script>";
        }
        break;

    case 'ver_detalle':
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $prodController->mostrarDetalle($id);
        break;

    case 'agregar_carrito':
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        $id_producto = ($data && isset($data['id'])) ? intval($data['id']) : (isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['id_producto']) ? intval($_POST['id_producto']) : 0));
        $cantidad    = ($data && isset($data['cantidad'])) ? intval($data['cantidad']) : 1;

        if ($id_producto > 0) {
            if (!isset($_SESSION['carrito'])) $_SESSION['carrito'] = [];
            $_SESSION['carrito'][$id_producto] = ($_SESSION['carrito'][$id_producto] ?? 0) + $cantidad;
            echo json_encode(['success' => true, 'cantidadTotal' => array_sum($_SESSION['carrito'])]);
        } else {
            echo json_encode(['success' => false, 'error' => 'ID recibido: ' . $id_producto]);
        }
        exit();

    case 'ver_carrito': $prodController->mostrarCarrito(); break;
    
    case 'actualizar_cantidad':
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $op = $_GET['operacion'] ?? '';
        if ($id > 0 && isset($_SESSION['carrito'][$id])) {
            if ($op === 'sumar') $_SESSION['carrito'][$id]++;
            elseif ($op === 'restar') {
                $_SESSION['carrito'][$id]--;
                if ($_SESSION['carrito'][$id] <= 0) unset($_SESSION['carrito'][$id]);
            }
        }
        header('Location: index.php?action=ver_carrito');
        exit();

    case 'eliminar_carrito':
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id > 0) unset($_SESSION['carrito'][$id]);
        header('Location: index.php?action=ver_carrito');
        exit();

    case 'procesar_pago':
        if (!isset($_SESSION['usuario_id'])) { header('Location: index.php?action=login'); exit(); }
        if (empty($_SESSION['carrito'])) { header('Location: index.php?action=catalogo'); exit(); }
        require_once 'views/pago.php';
        break;

    case 'finalizar_orden':
        if (!isset($_SESSION['usuario_id']) || empty($_SESSION['carrito'])) {
            header('Location: index.php?action=catalogo');
            exit();
        }

        try {
            // 1. Preparar datos para la factura
            $items_factura = [];
            $subtotal_calculado = 0;
            $envio_factura = (float)($_POST['tarifa_envio'] ?? 0);

            foreach ($_SESSION['carrito'] as $id => $cantidad) {
                $p = $productoModel->buscarPorId($id);
                if ($p) {
                    $sub = (float)$p['precio'] * (int)$cantidad;
                    $items_factura[] = [
                        'nombre'   => $p['nombre'],
                        'cantidad' => $cantidad,
                        'precio'   => (float)$p['precio'],
                        'subtotal' => $sub
                    ];
                    $subtotal_calculado += $sub;
                }
            }
            $total_factura = $subtotal_calculado + $envio_factura;

            // 2. Registrar pedido
            $id_pedido = $pedidoModel->registrarPedidoCompleto($_SESSION['usuario_id'], $total_factura, $_SESSION['carrito'], $productoModel);

            // 3. Generar PDF
            $dompdf = new \Dompdf\Dompdf();
            ob_start();
            // Incluimos la vista, las variables $items_factura, $subtotal_calculado, $envio_factura y $total_factura 
            // ya están definidas arriba y son accesibles aquí.
            require 'views/factura_template.php'; 
            $html = ob_get_clean();
            
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $pdfOutput = $dompdf->output();

            // 4. Enviar correo
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'clotchproyectos@gmail.com'; 
            $mail->Password   = 'mknbuhhuiqgojwtr'; 
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';
            $mail->setFrom('clotchproyectos@gmail.com', 'IGNIT Performance');
            $mail->addAddress($_SESSION['usuario_email']); 
            $mail->addStringAttachment($pdfOutput, 'Factura_IGNIT.pdf', 'base64', 'application/pdf');
            $mail->isHTML(true);
            $mail->Subject = 'Factura Electrónica - IGNIT Performance';
            $mail->Body    = 'Hola ' . $_SESSION['usuario_nombre'] . ', gracias por tu compra.';
            $mail->send();

            // 5. Limpiar y redirigir
            unset($_SESSION['carrito']);
            header("Location: index.php?action=rastrear_pedido&id=" . $id_pedido);
            exit();
        } catch (Exception $e) {
            die("Error en el despacho: " . $e->getMessage());
        }
    case 'mis_pedidos': $prodController->mostrarMisPedidos(); break;

    case 'rastrear_pedido':
        $id_pedido = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $pedido = $pedidoModel->obtenerPorId($id_pedido);
        
        if (!$pedido || (isset($pedido['id_usuario']) && $pedido['id_usuario'] != $_SESSION['usuario_id'])) {
            die("Acceso no autorizado.");
        }

        $id_key    = isset($pedido['id_pedido']) ? 'id_pedido' : 'id';
        $total_key = isset($pedido['monto_total']) ? 'monto_total' : 'total';
        $est_key   = isset($pedido['status']) ? 'status' : 'estado';

        $codigo_rastreo = "IG-ORD-" . ($pedido[$id_key] ?? '0');
        $total_pedido   = $pedido[$total_key] ?? 0;
        $estado_actual  = $pedido[$est_key] ?? 'pendiente';
        
        require_once 'views/rastreo.php';
        break;

    case 'catalogo':
    default:
        $prodController->mostrarCatalogo();
        break;
}