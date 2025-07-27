<?php
// Se define la ruta base de la aplicación si no está definida.
if (!defined('APP_PATH')) {
    define('APP_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once APP_PATH . "/admin/modelo/UsuarioDAO.php";
require_once APP_PATH . "/config/csrf.php"; // Incluye la utilidad CSRF

class AuthController {
    public function login() {
        $error = null;

        // Genera el token CSRF para el formulario de login (si es una solicitud GET)
        // o si hay un error de validación y se vuelve a mostrar el formulario.
        $csrf_token = generate_csrf_token(); 

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 1. Validar el token CSRF al recibir el POST
            $csrf_token_sent = $_POST[CSRF_TOKEN_NAME] ?? '';
            if (!validate_csrf_token($csrf_token_sent)) {
                $error = "Error de seguridad: Token CSRF inválido. Por favor, recargue la página.";
                // No se sale de la ejecución, se vuelve a mostrar el formulario con el error.
            } else {
                // Si el token CSRF es válido, procede con la autenticación
                $correo = trim($_POST['correo'] ?? ''); // Sanear input
                $contrasena = $_POST['contrasena'] ?? ''; // No sanear la contraseña directamente, se usa para hash_verify

                // Validación básica de inputs (adicional a la validación CSRF)
                if (empty($correo) || empty($contrasena)) {
                    $error = "Por favor, ingrese su correo y contraseña.";
                } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                    $error = "Formato de correo electrónico inválido.";
                } else {
                    $dao = new UsuarioDAO();
                    $usuario = $dao->autenticar($correo, $contrasena);

                    if ($usuario) {
                        // La sesión ya se inició en admin/index.php
                        $_SESSION['usuario'] = $usuario['correo'];
                        // Regenerar ID de sesión para prevenir Session Fixation
                        session_regenerate_id(true); 
                        header("Location: index.php?action=listar");
                        exit();
                    } else {
                        $error = "Correo o contraseña incorrectos.";
                    }
                }
            }
        }

        // Pasa el token CSRF a la vista de login
        include APP_PATH . "/admin/vista/login.php";
    }

    public function logout() {
        // La sesión ya se inició en admin/index.php
        session_destroy();
        // Regenerar ID de sesión al destruir para asegurar que no haya residuos
        session_regenerate_id(true); 
        header("Location: index.php?action=login");
        exit();
    }
}