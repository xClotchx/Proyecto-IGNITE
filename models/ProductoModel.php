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
}