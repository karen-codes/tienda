<?php
// Define la ruta base de la aplicación.
// Esto es crucial para que los 'require_once' funcionen correctamente.
if (!defined('APP_PATH')) {
    define('APP_PATH', dirname(dirname(__FILE__)));
}

// Inicia la sesión al principio de cada script que la necesite.
// Esto es importante para la autenticación y los tokens CSRF.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Incluye la utilidad CSRF.
require_once APP_PATH . "/config/csrf.php";

// Define las acciones que requieren autenticación.
$accionesProtegidas = ['listar', 'registrar', 'eliminar', 'editar', 'logout'];

// Obtiene la acción solicitada.
$action = $_GET['action'] ?? 'login'; // Por defecto, la acción es 'login'

// --- Autenticación y Protección CSRF ---

// Verifica si la acción requiere autenticación.
if (in_array($action, $accionesProtegidas) && !isset($_SESSION['usuario'])) {
    // Si la acción está protegida y el usuario no está logueado, redirige al login.
    header("Location: index.php?action=login");
    exit();
}

// Valida el token CSRF para todas las solicitudes POST, excepto para el login inicial
// (ya que el token se genera en la vista de login y se valida al enviar).
// NOTA: Para el login, la validación CSRF se manejará dentro de AuthController.php.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action !== 'login') {
    $csrf_token_sent = $_POST[CSRF_TOKEN_NAME] ?? '';
    if (!validate_csrf_token($csrf_token_sent)) {
        // Si el token es inválido, detiene la ejecución y muestra un error.
        http_response_code(403); // Forbidden
        die("Error de seguridad: Token CSRF inválido. Por favor, recargue la página e intente de nuevo.");
    }
}

// --- Enrutamiento del Controlador ---

// Se incluyen los controladores necesarios.
require_once APP_PATH . "/admin/controlador/AuthController.php";
require_once APP_PATH . "/admin/controlador/ProductoController.php";
// Si tienes otros controladores, inclúyelos aquí.

// Instancia los controladores.
$authController = new AuthController();
$productoController = new ProductoController();

// Maneja las diferentes acciones.
switch ($action) {
    case 'login':
        $authController->login();
        break;
    case 'logout':
        $authController->logout();
        break;
    case 'listar':
        $productoController->listar();
        break;
    case 'registrar':
        $productoController->registrar();
        break;
    case 'eliminar':
        $productoController->eliminar();
        break;
    case 'editar':
        $productoController->editar();
        break;
    default:
        // Si la acción no es reconocida, redirige al login o muestra un error 404.
        header("Location: index.php?action=login");
        exit();
        // Opcional: http_response_code(404); echo "Página de administración no encontrada.";
        break;
}
