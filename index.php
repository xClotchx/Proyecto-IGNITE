<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Rutas directas desde la raíz
require_once 'controllers/ProductosController.php';

$controller = new ProductosController();
$controller->mostrarCatalogo();