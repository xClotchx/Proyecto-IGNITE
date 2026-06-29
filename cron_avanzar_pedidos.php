<?php
// Configuración directa de base de datos
$host = 'localhost';
$db   = 'proyecto'; // Asegúrate de que este sea el nombre real de tu BD
$user = 'Eadmin';   // Tu usuario
$pass = '12345';    // Tu contraseña

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta corregida usando JOIN
    $sql = "SELECT p.id_pedido, p.estado_pedido, u.email 
            FROM Pedidos p 
            JOIN Usuarios u ON p.id_usuario = u.id_usuario 
            WHERE p.estado_pedido != 'entregado'";
            
    $stmt = $pdo->query($sql);
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $orden = ['pendiente', 'preparando', 'en_camino', 'ultima_milla', 'entregado'];

    foreach ($pedidos as $pedido) {
        $key = array_search($pedido['estado_pedido'], $orden);
        if ($key !== false && $key < count($orden) - 1) {
            $nuevoEstado = $orden[$key + 1];
            $update = $pdo->prepare("UPDATE Pedidos SET estado_pedido = ? WHERE id_pedido = ?");
            $update->execute([$nuevoEstado, $pedido['id_pedido']]);
        }
    }
} catch (Exception $e) {
    // Si hay error, no mostramos nada para no romper el cron
    error_log("Error en cron: " . $e->getMessage());
}
?>