<?php
header('Content-Type: application/json');

// Conexión a la base de datos del BANCO (puerto/BD diferente)
try {
    $pdo_banco = new PDO("mysql:host=localhost;dbname=banco;charset=utf8mb4", "bancoc", "12345");
    $pdo_banco->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['aprobado' => false, 'mensaje' => 'Error de conexión con el banco.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['tarjeta']) || !isset($data['monto'])) {
    echo json_encode(['aprobado' => false, 'mensaje' => 'Datos incompletos.']);
    exit;
}

try {
    // Buscar tarjeta con datos del titular
    $stmt = $pdo_banco->prepare("
        SELECT c.saldo, c.cvv, c.fecha_expiracion, u.nombre, u.apellido
        FROM Cuentas_Bancarias c
        JOIN Usuarios u ON c.id_usuario = u.id_usuario
        WHERE c.numero_tarjeta = ?
    ");
    $stmt->execute([$data['tarjeta']]);
    $cuenta = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cuenta) {
        echo json_encode(['aprobado' => false, 'mensaje' => 'Tarjeta no encontrada.']);
        exit;
    }

    // Verificar CVV
    if (trim($data['cvv']) !== trim($cuenta['cvv'])) {
        echo json_encode(['aprobado' => false, 'mensaje' => 'CVV incorrecto.']);
        exit;
    }

    // Verificar fecha
    $partes = explode('/', trim($data['fecha_expiracion']));
    $mes_ing  = (int)$partes[0];
    $anio_ing = (int)$partes[1];
    if ($anio_ing < 100) $anio_ing += 2000;

    $partes_bd = explode('/', trim($cuenta['fecha_expiracion']));
    $mes_bd    = (int)$partes_bd[0];
    $anio_bd   = (int)$partes_bd[1];
    if ($anio_bd < 100) $anio_bd += 2000;

    if ($mes_ing !== $mes_bd || $anio_ing !== $anio_bd) {
        echo json_encode(['aprobado' => false, 'mensaje' => 'Fecha de vencimiento incorrecta.']);
        exit;
    }

    if ($anio_bd < (int)date('Y') || ($anio_bd === (int)date('Y') && $mes_bd < (int)date('m'))) {
        echo json_encode(['aprobado' => false, 'mensaje' => 'La tarjeta está vencida.']);
        exit;
    }

    // Verificar nombre
    $limpiar = function($str) {
        $str = mb_strtolower(trim($str), 'UTF-8');
        $str = preg_replace('/\s+/', ' ', $str);
        return str_replace(['á','é','í','ó','ú','ü','ñ'], ['a','e','i','o','u','u','n'], $str);
    };

    if ($limpiar($data['nombre_titular']) !== $limpiar($cuenta['nombre'] . ' ' . $cuenta['apellido'])) {
        echo json_encode(['aprobado' => false, 'mensaje' => 'El nombre del titular no coincide.']);
        exit;
    }

    // Verificar saldo
    if ((float)$cuenta['saldo'] < (float)$data['monto']) {
        echo json_encode(['aprobado' => false, 'mensaje' => 'Fondos insuficientes.']);
        exit;
    }

    // Descontar saldo
    $pdo_banco->prepare("UPDATE Cuentas_Bancarias SET saldo = saldo - ? WHERE numero_tarjeta = ?")
              ->execute([$data['monto'], $data['tarjeta']]);

    echo json_encode(['aprobado' => true, 'mensaje' => 'Pago procesado exitosamente.']);

} catch (Exception $e) {
    echo json_encode(['aprobado' => false, 'mensaje' => $e->getMessage()]);
}