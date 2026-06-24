<?php
// Quita el ../ para buscar la carpeta config desde la raíz
require_once 'config/conexion.php';

class ProductoModel {
    private $conexion;
    private $tabla = "Productos";

    public function __construct() {
        $database = new Conexion();
        $this->conexion = $database->conectar();
    }

    // Método para obtener todos los productos para el catálogo
    public function obtenerTodos() {
        $query = "SELECT p.id_producto, p.nombre, p.descripcion, p.precio, p.stock_inventario, c.nombre_categoria 
                  FROM $this->tabla p 
                  LEFT JOIN Categorias c ON p.id_categoria = c.id_categoria";
        
        $stmt = $this->conexion->prepare($query);
        $stmt->execute();
        
        // Retorna un array asociativo limpio
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ==========================================================================
    // NUEVO: Método para buscar un solo producto por su ID (Usado para el Carrito)
    // ==========================================================================
    public function buscarPorId($id) {
        $query = "SELECT p.id_producto, p.nombre, p.descripcion, p.precio, p.stock_inventario, c.nombre_categoria 
                  FROM $this->tabla p 
                  LEFT JOIN Categorias c ON p.id_categoria = c.id_categoria
                  WHERE p.id_producto = :id 
                  LIMIT 1";
        
        $stmt = $this->conexion->prepare($query);
        // Vinculamos el parámetro de forma segura para evitar inyecciones SQL
        $stmt->execute([':id' => $id]);
        
        // Retorna la fila del producto o false si no encuentra nada
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ==========================================================================
    // NUEVO: Método para disminuir el stock al confirmar una compra
    // ==========================================================================
    public function disminuirStock($id_producto, $cantidad_comprada) {
        // SQL blindado: solo resta si el stock_inventario es mayor o igual a la cantidad que se quiere llevar
        $query = "UPDATE $this->tabla 
                  SET stock_inventario = stock_inventario - :cantidad 
                  WHERE id_producto = :id AND stock_inventario >= :cantidad";
        
        $stmt = $this->conexion->prepare($query);
        
        $stmt->execute([
            ':cantidad' => $cantidad_comprada,
            ':id'       => $id_producto
        ]);

        // Si rowCount() devuelve 1, la actualización fue exitosa. Si devuelve 0, falló por falta de stock.
        return $stmt->rowCount() > 0;
    }
}