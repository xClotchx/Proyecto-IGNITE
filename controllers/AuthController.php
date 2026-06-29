<?php
require_once 'models/UsuarioModel.php';

class AuthController {

    public function registrarUsuario() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre      = trim($_POST['nombre'] ?? '');
            $apellido    = trim($_POST['apellido'] ?? '');
            $email       = trim($_POST['email'] ?? '');
            $contrasenia = $_POST['contrasenia'] ?? '';
            $direccion   = trim($_POST['direccion'] ?? '');
            $telefono    = trim($_POST['telefono'] ?? '');
            $pais        = trim($_POST['pais'] ?? 'Panamá');

            if (empty($nombre) || empty($apellido) || empty($email) || 
                empty($contrasenia) || empty($direccion) || empty($telefono) || empty($pais)) {
                $error = "Todos los campos son obligatorios.";
                require_once 'views/registro.php';
                return;
            }

            $usuarioModel = new UsuarioModel();
            $resultado = $usuarioModel->registrar($nombre, $apellido, $email, $contrasenia, $direccion, $telefono, $pais);

            if ($resultado === true) {
                header('Location: index.php?action=login');
                exit();
            } elseif ($resultado === 'duplicado') {
                $error_registro = 'duplicado';
                require_once 'views/registro.php';
            } else {
                $error = "Error al registrar. Inténtalo de nuevo.";
                require_once 'views/registro.php';
            }
        } else {
            require_once 'views/registro.php';
        }
    }

    public function iniciarSesion() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $contrasenia = $_POST['contrasenia'] ?? '';

            $usuarioModel = new UsuarioModel();
            $usuario = $usuarioModel->buscarPorCorreo($email);

            if ($usuario && password_verify($contrasenia, $usuario['contrasenia'])) {
                if (session_status() === PHP_SESSION_NONE) { session_start(); }
                
                $_SESSION['usuario_id']        = $usuario['id_usuario'];
                $_SESSION['usuario_nombre']    = $usuario['nombre'];
                $_SESSION['usuario_apellido']  = $usuario['apellido'];
                $_SESSION['usuario_email']     = $usuario['email'];
                $_SESSION['usuario_telefono']  = $usuario['telefono'];
                $_SESSION['usuario_direccion'] = $usuario['direccion'];
                $_SESSION['usuario_pais']      = $usuario['pais'];

                header('Location: index.php');
                exit();
            } else {
                $error = "Email o contraseña incorrectos.";
                require_once 'views/login.php';
            }
        }
    }

    // --- MÉTODOS PARA EDITAR PERFIL ---

    public function editarPerfil() {
        if (!isset($_SESSION['usuario_id'])) { 
            header('Location: index.php?action=login'); 
            exit(); 
        }
        $usuarioModel = new UsuarioModel();
        // Obtenemos los datos actuales para llenar el formulario
        $usuario = $usuarioModel->buscarPorId($_SESSION['usuario_id']);
        require_once 'views/editar_perfil.php';
    }

    public function procesarEdicion() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $uModel = new UsuarioModel();
            
            // Actualizamos la base de datos
            $uModel->actualizarPerfil(
                $_SESSION['usuario_id'], 
                $_POST['nombre'], 
                $_POST['apellido'], 
                $_POST['direccion'], 
                $_POST['telefono'], 
                $_POST['pais']
            );
            
            // Refrescamos la sesión para que el cambio se vea al instante
            $_SESSION['usuario_nombre']    = $_POST['nombre'];
            $_SESSION['usuario_apellido']  = $_POST['apellido'];
            $_SESSION['usuario_direccion'] = $_POST['direccion'];
            $_SESSION['usuario_telefono']  = $_POST['telefono'];
            $_SESSION['usuario_pais']      = $_POST['pais'];
            
            header('Location: index.php');
            exit();
        }
    }

    public function cerrarSesion() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        session_destroy();
        header('Location: index.php');
        exit();
    }
}