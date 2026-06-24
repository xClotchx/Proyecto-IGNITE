<?php
class PedidoModel {
    private $db;

    public function __construct() {
        try {
            // Ajusta las credenciales según tu configuración local de MariaDB
            $this->db = new PDO("mysql:host=localhost;dbname=proyecto;charset=utf8", "Eadmin", "12345", [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        } catch (PDOException $e) {
            die("Error de conexión en IGNIT: " . $e->getMessage());
        }
    }

    // Procesa e inserta la Orden Completa utilizando Transacciones
    public function registrarPedidoCompleto($id_usuario, $total, $carrito, $productoModel) {
        try {
            // Iniciamos la transacción para proteger la integridad de los datos
            $this->db->beginTransaction();

            // 1. Insertar la cabecera en la tabla 'Pedidos'
            $sqlPedido = "INSERT INTO Pedidos (id_usuario, fecha_pedido, estado_pedido, total) 
                          VALUES (:id_usuario, CURRENT_TIMESTAMP(), 'pendiente', :total)";
            
            $stmtPedido = $this->db->prepare($sqlPedido);
            $stmtPedido->execute([
                ':id_usuario' => $id_usuario,
                ':total' => $total
            ]);

            // Obtenemos el ID autoincrementable generado para este pedido
            $id_pedido = $this->db->lastInsertId();

            // 2. Preparar la sentencia para la tabla 'Detalle_Pedido'
            $sqlDetalle = "INSERT INTO Detalle_Pedido (id_pedido, id_producto, cantidad, precio_unitario) 
                           VALUES (:id_pedido, :id_producto, :cantidad, :precio_unitario)";
            $stmtDetalle = $this->db->prepare($sqlDetalle);

            // 3. Recorrer el carrito e insertar cada componente
            foreach ($carrito as $id_producto => $cantidad) {
                // Buscamos el precio actual del producto en la base de datos para evitar alteraciones en el cliente
                $producto = $productoModel->buscarPorId($id_producto);
                
                if (!$producto) {
                    throw new Exception("El componente con ID $id_producto ya no se encuentra disponible en el catálogo.");
                }

                $stmtDetalle->execute([
                    ':id_pedido'       => $id_pedido,
                    ':id_producto'     => $id_producto,
                    ':cantidad'        => $cantidad,
                    ':precio_unitario' => $producto['precio']
                ]);
            }

            // Si todo salió bien, confirmamos los cambios de forma definitiva
            $this->db->commit();
            return $id_pedido;

        } catch (Exception $e) {
            // Si algo falla en el bucle o en las tablas, deshacemos todo para no dejar registros huérfanos
            $this->db->rollBack();
            throw $e;
        }
    }

    public function obtenerPorId($id_pedido) {
        $sql = "SELECT * FROM Pedidos WHERE id_pedido = :id_pedido";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_pedido' => $id_pedido]);
        return $stmt->fetch();
    }

    // ==========================================================================
    // AGREGADO: Método que le faltaba a tu modelo para alimentar el controlador
    // ==========================================================================
    public function obtenerPedidosPorUsuario($id_usuario) {
        $sql = "SELECT * FROM Pedidos WHERE id_usuario = :id_usuario ORDER BY fecha_pedido DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_usuario' => $id_usuario]);
        return $stmt->fetchAll();
    }
}