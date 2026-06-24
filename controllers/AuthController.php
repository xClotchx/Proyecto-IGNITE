<?php
require_once 'models/UsuarioModel.php';

class AuthController {

    public function registrarUsuario() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre']);
            $apellido = trim($_POST['apellido']);
            $email = trim($_POST['email']);
            $contrasenia = $_POST['contrasenia'];
            $telefono = !empty($_POST['telefono']) ? trim($_POST['telefono']) : null;

            $usuarioModel = new UsuarioModel();
            
            // Guardamos la respuesta del modelo para evaluar qué pasó
            $resultado = $usuarioModel->registrar($nombre, $apellido, $email, $contrasenia, $telefono);

            if ($resultado === true) {
                // Registro exitoso, redirige normalmente al login
                header('Location: index.php?action=login');
                exit();
            } elseif ($resultado === 'duplicado') {
                // MODIFICADO: Captura el correo duplicado y activa el aviso flotante en la vista
                $error_registro = 'duplicado';
                require_once 'views/registro.php';
            } else {
                // Cualquier otro fallo genérico de la base de datos
                $error = "Error al registrar. Inténtalo de nuevo.";
                require_once 'views/registro.php';
            }
        } else {
            // Si entran por GET a la ruta, cargamos la vista limpia
            require_once 'views/registro.php';
        }
    }

    public function iniciarSesion() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $contrasenia = $_POST['contrasenia'];

            $usuarioModel = new UsuarioModel();
            $usuario = $usuarioModel->buscarPorCorreo($email);

            // Validamos contra tu columna 'contrasenia'
            if ($usuario && password_verify($contrasenia, $usuario['contrasenia'])) {
                $_SESSION['usuario_id'] = $usuario['id_usuario'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];

                header('Location: index.php');
                exit();
            } else {
                $error = "Email o contraseña incorrectos.";
                require_once 'views/login.php';
            }
        }
    }

    public function cerrarSesion() {
        session_destroy();
        header('Location: index.php');
        exit();
    }
}