<?php
// Subimos un nivel de carpeta para encontrar vendor en la raíz del proyecto
require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class UsuarioModel {
    private $conexion;

    public function __construct() {
        $this->conexion = new PDO("mysql:host=localhost;dbname=proyecto;charset=utf8", "Eadmin", "12345");
        $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    // Registrar usuario y enviar notificación por correo
    public function registrar($nombre, $apellido, $email, $contrasenia, $telefono = null) {
        try {
            $passwordHash = password_hash($contrasenia, PASSWORD_BCRYPT);

            $sql = "INSERT INTO Usuarios (nombre, apellido, email, contrasenia, telefono) 
                    VALUES (:nombre, :apellido, :email, :contrasenia, :telefono)";
            
            $stmt = $this->conexion->prepare($sql);
            
            if ($stmt->execute([
                ':nombre' => $nombre,
                ':apellido' => $apellido,
                ':email' => $email,
                ':contrasenia' => $passwordHash,
                ':telefono' => $telefono
            ])) {
                
                // Disparamos el correo pasándole los datos del nuevo usuario
                $this->enviarNotificacionRegistro($nombre, $apellido, $email, $telefono);
                return true;
            }
            return false;

        } catch (PDOException $e) {
            // Código SQLSTATE 23000 es para violaciones de integridad (llaves duplicadas)
            if ($e->getCode() == 23000) {
                return 'duplicado';
            }
            // Si es otro tipo de error, lo relanzamos
            throw $e;
        }
    }

    private function enviarNotificacionRegistro($nombre, $apellido, $email, $telefono) {
        $mail = new PHPMailer(true);

        try {
            // Configuración del Servidor SMTP (Gmail)
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'clotchproyectos@gmail.com';  
            $mail->Password   = 'mknbuhhuiqgojwtr';        
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Remitente y Destinatario Dinámico
            $mail->setFrom('clotchproyecto@gmail.com', 'IGNIT Performance');
            $mail->addAddress($email, $nombre . ' ' . $apellido); 

            // Contenido con Estilo Premium Oscuro
            $mail->isHTML(true);
            $mail->Subject = '¡Bienvenido a IGNIT Performance, ' . $nombre . '!';
            
            $mail->Body    = "
                <div style='background-color: #121212; color: #f5f5f7; padding: 40px; font-family: sans-serif; border-radius: 8px; max-width: 500px; margin: 0 auto; border: 1px solid #333;'>
                    <h2 style='color: #e67e22; border-bottom: 1px solid #333; padding-bottom: 15px; margin-top: 0; text-transform: uppercase; letter-spacing: 1px;'>
                        ¡Tu cuenta ha sido creada!
                    </h2>
                    <p style='font-size: 1.1rem; line-height: 1.6; color: #f5f5f7;'>Hola <strong>$nombre</strong>,</p>
                    <p style='font-size: 1rem; line-height: 1.5; color: #8e8e93;'>
                        Gracias por registrarte en <strong>IGNIT Performance</strong>. Tu cuenta ya está activa y lista para ser usada.
                    </p>
                    <div style='background: #1e1e1e; padding: 20px; border-radius: 6px; margin: 25px 0; border: 1px solid #262626;'>
                        <h4 style='margin-top: 0; color: #e67e22; margin-bottom: 10px;'>Detalles de tu cuenta:</h4>
                        <p style='margin: 5px 0; font-size: 0.9rem; color: #8e8e93;'><strong>Usuario:</strong> $email</p>
                        <p style='margin: 5px 0; font-size: 0.9rem; color: #8e8e93;'><strong>Estado:</strong> Activo / Piloto</p>
                    </div>
                    <hr style='border: 0; border-top: 1px solid #333; margin: 30px 0;'>
                    <p style='font-size: 0.8rem; color: #666; text-align: center; margin-bottom: 0;'>
                        Este es un correo automático de bienvenida. Por favor no respondas a esta dirección.
                    </p>
                </div>
            ";

            $mail->send();
        } catch (Exception $e) {
            error_log("No se pudo enviar el correo de bienvenida. Error: {$mail->ErrorInfo}");
        }
    }

    public function buscarPorCorreo($email) {
        $sql = "SELECT * FROM Usuarios WHERE email = :email";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}