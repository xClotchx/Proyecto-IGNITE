<?php
// Si ves esto en pantalla, el router funciona perfecto
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'vendor/autoload.php';
require_once 'controllers/ProductosController.php';
require_once 'controllers/AuthController.php';
require_once 'models/ProductoModel.php';
require_once 'models/PedidoModel.php';

$action = $_GET['action'] ?? 'catalogo';

$prodController = new ProductosController();
$authController = new AuthController();
$productoModel  = new ProductoModel();
$pedidoModel    = new PedidoModel();

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
        if (ob_get_length()) ob_end_clean();
        header('Content-Type: application/json');

        try {
            // 0. VERIFICACIÓN DE CAMPOS
            $campos_requeridos = ['nombre_tarjeta', 'fecha_vence', 'numero_tarjeta', 'cvv', 'total_final'];
            foreach ($campos_requeridos as $campo) {
                if (empty($_POST[$campo])) throw new Exception("Campo requerido faltante: $campo");
            }

            if (!isset($_SESSION['usuario_id']) || empty($_SESSION['carrito'])) {
                throw new Exception("Sesión expirada o carrito vacío.");
            }

            // 1. VALIDACIÓN DE FORMATO LOCAL
            $num_tarjeta    = str_replace(' ', '', $_POST['numero_tarjeta']);
            $cvv            = trim($_POST['cvv']);
            $fecha_vence    = trim($_POST['fecha_vence']);
            $nombre_tarjeta = trim($_POST['nombre_tarjeta']);
            $monto          = (float)$_POST['total_final'];
            $tarifa_envio   = (float)($_POST['tarifa_envio'] ?? 0);

            if (!preg_match('/^\d{16}$/', $num_tarjeta)) {
                throw new Exception("Número de tarjeta inválido. Debe tener 16 dígitos.");
            }
            if (!preg_match('/^\d{3,4}$/', $cvv)) {
                throw new Exception("CVV inválido. Debe tener 3 o 4 dígitos.");
            }
            if (!preg_match('/^\d{2}\/\d{2}$/', $fecha_vence)) {
                throw new Exception("Formato de fecha inválido. Use MM/AA.");
            }
            if (empty($nombre_tarjeta)) {
                throw new Exception("Ingresa el nombre del titular de la tarjeta.");
            }

            // 2. VALIDACIÓN DIRECTA CON LA BD DEL BANCO
            try {
                $pdo_banco = new PDO("mysql:host=localhost;dbname=banco;charset=utf8mb4", "bancoc", "12345");
                $pdo_banco->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                throw new Exception("No se pudo conectar con el banco.");
            }

            $stmt = $pdo_banco->prepare("
                SELECT c.saldo, c.cvv, c.fecha_expiracion, u.nombre, u.apellido
                FROM Cuentas_Bancarias c
                JOIN Usuarios u ON c.id_usuario = u.id_usuario
                WHERE c.numero_tarjeta = ?
            ");
            $stmt->execute([$num_tarjeta]);
            $cuenta_banco = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$cuenta_banco) {
                throw new Exception("Tarjeta no encontrada en el banco.");
            }
            if (trim($cvv) !== trim($cuenta_banco['cvv'])) {
                throw new Exception("CVV incorrecto.");
            }

            $partes   = explode('/', trim($fecha_vence));
            $mes_ing  = (int)$partes[0];
            $anio_ing = (int)$partes[1];
            if ($anio_ing < 100) $anio_ing += 2000;

            $partes_bd = explode('/', trim($cuenta_banco['fecha_expiracion']));
            $mes_bd    = (int)$partes_bd[0];
            $anio_bd   = (int)$partes_bd[1];
            if ($anio_bd < 100) $anio_bd += 2000;

            if ($mes_ing !== $mes_bd || $anio_ing !== $anio_bd) {
                throw new Exception("Fecha de vencimiento incorrecta.");
            }
            if ($anio_bd < (int)date('Y') || ($anio_bd === (int)date('Y') && $mes_bd < (int)date('m'))) {
                throw new Exception("La tarjeta está vencida.");
            }

            $limpiar = function($str) {
                $str = mb_strtolower(trim($str), 'UTF-8');
                $str = preg_replace('/\s+/', ' ', $str);
                return str_replace(['á','é','í','ó','ú','ü','ñ'], ['a','e','i','o','u','u','n'], $str);
            };

            if ($limpiar($nombre_tarjeta) !== $limpiar($cuenta_banco['nombre'] . ' ' . $cuenta_banco['apellido'])) {
                throw new Exception("El nombre del titular no coincide con el registrado en el banco.");
            }
            if ((float)$cuenta_banco['saldo'] < $monto) {
                throw new Exception("Fondos insuficientes. Saldo disponible: $" . number_format($cuenta_banco['saldo'], 2));
            }

            // Descontar saldo en el banco
            $pdo_banco->prepare("UPDATE Cuentas_Bancarias SET saldo = saldo - ? WHERE numero_tarjeta = ?")
                      ->execute([$monto, $num_tarjeta]);

            // 3. REGISTRAR PEDIDO
            $id_pedido = $pedidoModel->registrarPedidoCompleto($_SESSION['usuario_id'], $monto, $_SESSION['carrito'], $productoModel);

            // 4. PREPARAR VARIABLES PARA LA FACTURA
            $items_factura      = [];
            $subtotal_calculado = 0;
            $envio_factura      = $tarifa_envio;

            foreach ($_SESSION['carrito'] as $id_prod => $cant) {
                $prod = $productoModel->buscarPorId($id_prod);
                if ($prod) {
                    $subtotal_item       = (float)$prod['precio'] * (int)$cant;
                    $subtotal_calculado += $subtotal_item;
                    $items_factura[]     = [
                        'nombre'   => $prod['nombre'],
                        'cantidad' => $cant,
                        'precio'   => (float)$prod['precio'],
                        'subtotal' => $subtotal_item
                    ];
                }
            }

            $total_factura = $subtotal_calculado + $envio_factura;

            // 5. GENERAR PDF Y ENVIAR EMAIL
            try {
                $dompdf = new \Dompdf\Dompdf();
                ob_start();
                require 'views/factura_template.php';
                $html = ob_get_clean();
                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();
                $pdfOutput = $dompdf->output();

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
                $mail->Body    = 'Hola ' . $_SESSION['usuario_nombre'] . ', tu pedido ha sido confirmado.';
                $mail->send();
            } catch (Exception $e_mail) {}

            unset($_SESSION['carrito']);
            echo json_encode(['success' => true, 'redirect' => 'index.php?action=rastrear_pedido&id=' . $id_pedido]);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'mensaje' => $e->getMessage()]);
        }
        exit();

    case 'mis_pedidos': $prodController->mostrarMisPedidos(); break;

    case 'rastrear_pedido':
        $id_pedido = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $pedido = $pedidoModel->obtenerPorId($id_pedido);

        // Si el pedido no existe o no pertenece al usuario, redirigimos limpiamente
        if (!$pedido || (isset($pedido['id_usuario']) && $pedido['id_usuario'] != $_SESSION['usuario_id'])) {
            header('Location: index.php?action=mis_pedidos');
            exit();
        }

        $codigo_rastreo = "IG-ORD-" . ($pedido['id_pedido'] ?? '0');
        $total_pedido   = $pedido['total'] ?? 0;
        $estado_actual  = $pedido['estado_pedido'] ?? 'pendiente';
        require_once 'views/rastreo.php';
        break;
        default:
        $prodController->mostrarCatalogo();
        break;
}

