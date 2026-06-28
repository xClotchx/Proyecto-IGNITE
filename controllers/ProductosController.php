<?php
// Carga del autoloader de Composer desde la raíz
require_once dirname(__DIR__) . '/vendor/autoload.php';

require_once 'models/ProductoModel.php';
require_once 'models/PedidoModel.php';

class ProductosController {

    public function mostrarCatalogo() {
        $productoModel = new ProductoModel();
        $productosRaw = $productoModel->obtenerTodos();
        $productos = [];
        foreach ($productosRaw as $prod) {
            $productos[] = [
                'id' => $prod['id_producto'],
                'nombre' => $prod['nombre'],
                'descripcion' => $prod['descripcion'],
                'categoria' => $prod['nombre_categoria'] ?? 'Sin Categoría',
                'precio' => $prod['precio'],
                'stock' => $prod['stock_inventario'],
                'imagen' => $prod['imagen'] ?? 'default.jpg'
            ];
        }
        require_once dirname(__DIR__) . '/views/catalogo.php';
    }

    // --- MÉTODO ACTUALIZADO PARA DETALLE CON RECOMENDACIONES ---
    public function mostrarDetalle($id) {
        $productoModel = new ProductoModel();
        $prod = $productoModel->buscarPorId($id);
        
        if (!$prod) {
            header('Location: index.php?action=catalogo');
            exit();
        }

        $producto = [
            'id' => $prod['id_producto'],
            'nombre' => $prod['nombre'],
            'descripcion' => $prod['descripcion'],
            'categoria' => $prod['nombre_categoria'] ?? 'Sin Categoría',
            'precio' => $prod['precio'],
            'stock' => $prod['stock_inventario'],
            'imagen' => $prod['imagen'] ?? 'default.jpg'
        ];

        // Obtener productos recomendados (20 al azar, excluyendo el actual)
        $todos = $productoModel->obtenerTodos();
        $otros = array_filter($todos, function($p) use ($id) {
            return $p['id_producto'] != $id;
        });
        
        shuffle($otros);
        $recomendados = array_map(function($p) {
            return [
                'id_producto' => $p['id_producto'],
                'nombre' => $p['nombre'],
                'precio' => $p['precio']
            ];
        }, array_slice($otros, 0, 20));

        require_once dirname(__DIR__) . '/views/detalle_producto.php';
    }
    // -----------------------------------------------------------

    public function mostrarCarrito() {
        $productoModel = new ProductoModel();
        $productos_carrito = [];
        if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
            foreach ($_SESSION['carrito'] as $id_producto => $cantidad) {
                $prod = $productoModel->buscarPorId($id_producto);
                if ($prod) {
                    $productos_carrito[] = [
                        'id' => $prod['id_producto'],
                        'nombre' => $prod['nombre'],
                        'precio' => $prod['precio'],
                        'cantidad' => $cantidad,
                        'categoria' => $prod['nombre_categoria'] ?? 'Sin Categoría'
                    ];
                }
            }
        }
        require_once dirname(__DIR__) . '/views/carrito.php';
    }

    public function finalizarCompra() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        $productoModel = new ProductoModel();
        
        try {
            $items = [];
            foreach ($_SESSION['carrito'] as $id_producto => $cantidad) {
                $prod = $productoModel->buscarPorId($id_producto);
                if ($prod) {
                    $items[] = [
                        'nombre'   => $prod['nombre'],
                        'cantidad' => $cantidad,
                        'precio'   => $prod['precio']
                    ];
                }
                
                if (!$productoModel->disminuirStock($id_producto, $cantidad)) {
                    throw new Exception("Stock insuficiente para el producto ID: $id_producto");
                }
            }

            $options = new \Dompdf\Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);
            
            $dompdf = new \Dompdf\Dompdf($options);
            
            ob_start();
            require dirname(__DIR__) . '/views/factura_template.php'; 
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
            $mail->Subject = 'Factura Electrónica - Confirmación de Compra';
            $mail->Body    = 'Hola, gracias por tu compra en IGNIT. Adjuntamos tu factura electrónica.';

            $mail->send();

            unset($_SESSION['carrito']);
            header('Location: index.php?action=mis_pedidos&status=success');
            exit();

        } catch (Exception $e) {
            die("Error en el proceso de pago: " . $e->getMessage());
        }
    }

    public function mostrarMisPedidos() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: index.php?action=login');
            exit();
        }
        $pedidoModel = new PedidoModel();
        $pedidos = $pedidoModel->obtenerPedidosPorUsuario($_SESSION['usuario_id']);
        require_once dirname(__DIR__) . '/views/mis_pedidos.php';
    }
}