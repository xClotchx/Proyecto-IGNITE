<?php
require_once 'models/ProductoModel.php';

class ProductosController {

    public function mostrarCatalogo() {
        $productoModel = new ProductoModel();
        
        // 1. Traemos los datos crudos de la base de datos (los que viste en pantalla)
        $productosRaw = $productoModel->obtenerTodos();
        
        // 2. Mapeamos los datos para que coincidan con las llaves que usa tu vista catalogo.php
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

        // 3. Cargamos la vista de forma limpia
        require_once 'views/catalogo.php';
    }
}