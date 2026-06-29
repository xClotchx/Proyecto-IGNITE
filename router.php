<?php
// router.php
$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

// Si el archivo existe físicamente, servirlo
if ($path !== '/' && file_exists(__DIR__ . $path)) {
    return false;
}

// FORZAR la ruta al index.php
require_once __DIR__ . '/index.php'; 
?>