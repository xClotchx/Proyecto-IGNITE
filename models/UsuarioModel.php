<?php
require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class UsuarioModel {
    private $conexion;

    public function __construct() {
        $this->conexion = new PDO("mysql:host=localhost;dbname=proyecto;charset=utf8", "Eadmin", "12345");
        $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    // 1. Registro actualizado incluyendo el campo 'pais'
    public function registrar($nombre, $apellido, $email, $contrasenia, $direccion, $telefono, $pais) {
        try {
            $passwordHash = password_hash($contrasenia, PASSWORD_BCRYPT);

            $sql = "INSERT INTO Usuarios (nombre, apellido, email, contrasenia, direccion, telefono, pais) 
                    VALUES (:nombre, :apellido, :email, :contrasenia, :direccion, :telefono, :pais)";
            
            $stmt = $this->conexion->prepare($sql);
            
            if ($stmt->execute([
                ':nombre'      => $nombre,
                ':apellido'    => $apellido,
                ':email'       => $email,
                ':contrasenia' => $passwordHash,
                ':direccion'   => $direccion,
                ':telefono'    => $telefono,
                ':pais'        => $pais
            ])) {
                $this->enviarNotificacionRegistro($nombre, $apellido, $email, $telefono, $direccion, $pais);
                return true;
            }
            return false;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) return 'duplicado';
            throw $e;
        }
    }

    // 2. Método para actualizar perfil
    public function actualizarPerfil($id, $nombre, $apellido, $direccion, $telefono, $pais) {
        $sql = "UPDATE Usuarios SET nombre = :nombre, apellido = :apellido, 
                direccion = :direccion, telefono = :telefono, pais = :pais 
                WHERE id_usuario = :id";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([
            ':id'        => $id, 
            ':nombre'    => $nombre, 
            ':apellido'  => $apellido,
            ':direccion' => $direccion, 
            ':telefono'  => $telefono, 
            ':pais'      => $pais
        ]);
    }

    // 3. Buscar por ID (necesario para cargar el formulario de edición)
    public function buscarPorId($id) {
        $sql = "SELECT * FROM Usuarios WHERE id_usuario = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function buscarPorCorreo($email) {
        $sql = "SELECT * FROM Usuarios WHERE email = :email";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function enviarNotificacionRegistro($nombre, $apellido, $email, $telefono, $direccion, $pais) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'clotchproyectos@gmail.com';  
            $mail->Password   = 'mknbuhhuiqgojwtr';        
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom('clotchproyectos@gmail.com', 'IGNIT Performance');
            $mail->addAddress($email, $nombre . ' ' . $apellido); 

            $mail->isHTML(true);
            $mail->Subject = '¡Bienvenido a IGNIT Performance, ' . $nombre . '!';
            $mail->Body    = "
                <div style='background-color: #121212; color: #f5f5f7; padding: 40px; font-family: sans-serif; border-radius: 8px;'>
                    <h2 style='color: #e67e22;'>¡Tu cuenta ha sido creada!</h2>
                    <p>Hola <strong>$nombre</strong>, tu cuenta ha sido registrada en $pais.</p>
                </div>
            ";
            $mail->send();
        } catch (Exception $e) {
            error_log("No se pudo enviar el correo: {$mail->ErrorInfo}");
        }
    }
}