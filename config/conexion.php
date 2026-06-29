<?php
class Conexion {
    // Apunta a tu propia máquina para que PHP encuentre MariaDB
    private $host = "localhost"; 
    private $db_name = "proyecto";
    private $username = "Eadmin"; // Tu usuario sin el "trador"
    private $password = "12345";   // Tu contraseña
    public $conn;

    public function conectar() {
        $this->conn = null;

        try {
            $opciones = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];

            // Al usar $this->host (127.0.0.1), PHP ya no se perderá en la red
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4", 
                $this->username, 
                $this->password,
                $opciones
            );

        } catch(PDOException $exception) {
            // Si MariaDB rechaza las credenciales, aquí te dirá "Access denied" en vez de romperse
            die("Error de conexión en IGNIT-DB: " . $exception->getMessage());
        }

        return $this->conn;
    }
}