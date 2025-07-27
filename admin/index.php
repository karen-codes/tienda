<?php
// Define la ruta base de la aplicación.
// Para admin/index.php, APP_PATH debe apuntar a la raíz de 'tienda'.
// Por lo tanto, subimos dos niveles desde la ubicación actual.
if (!defined('APP_PATH')) {
    define('APP_PATH', dirname(dirname(__FILE__)));
}

// Inicia la sesión al principio de cada script que la necesite.
// Esto es importante para la autenticación y los tokens CSRF.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Incluye las utilidades necesarias.
require_once APP_PATH . "/config/csrf.php"; // Para protección CSRF

// Define las acciones que requieren autenticación.
$accionesProtegidas = ['listar', 'registrar', 'eliminar', 'editar', 'logout', 'mensajes', 'pedidos', 'ver_detalle_pedido', 'actualizar_estado_pedido']; // Añadida 'pedidos', 'ver_detalle_pedido', 'actualizar_estado_pedido'

// Obtiene la acción solicitada.
$action = $_GET['action'] ?? 'login'; // Por defecto, la acción es 'login'

// --- Autenticación y Protección CSRF ---

// Verifica si la acción requiere autenticación.
if (in_array($action, $accionesProtegidas) && !isset($_SESSION['usuario'])) {
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

// --- Inclusión de Controladores ---
require_once APP_PATH . "/admin/controlador/AuthController.php";
require_once APP_PATH . "/admin/controlador/ProductoController.php";
require_once APP_PATH . "/admin/controlador/MensajeController.php"; // Cambiado a singular para MensajeController
require_once APP_PATH . "/admin/controlador/PedidoController.php"; // NUEVO: Incluye el controlador de pedidos

// Instancia los controladores.
$authController = new AuthController();
$productoController = new ProductoController();
$mensajesController = new MensajeController(); // Instancia el controlador de mensajes (singular)
$pedidoController = new PedidoController(); // NUEVO: Instancia el controlador de pedidos

// Se inicializa el contenido principal a vacío
$mainContent = '';

// Inicia el buffer de salida para capturar el contenido de la vista
ob_start();

// Maneja las diferentes acciones y carga la vista correspondiente
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
    case 'mensajes':
        $mensajesController->listarMensajes();
        break;
    case 'pedidos': // NUEVO: Acción para listar pedidos
        $pedidoController->listarPedidos();
        break;
    case 'ver_detalle_pedido': // NUEVO: Acción para ver detalles de un pedido
        $id = $_GET['id'] ?? null;
        $pedidoController->verDetallePedido($id);
        break;
    case 'actualizar_estado_pedido': // NUEVO: Acción para actualizar el estado de un pedido
        $pedidoController->actualizarEstado();
        break;
    default:
        // Redirige al login si la acción no es reconocida o no está autenticado
        header("Location: index.php?action=login");
        exit();
        break;
}

// Captura el contenido de la vista y lo almacena en $mainContent
$mainContent = ob_get_clean();

// Si la acción es 'login', 'logout' o cualquier otra que no deba usar el layout de dashboard,
// simplemente imprime el contenido capturado y termina.
if ($action === 'login' || $action === 'logout') {
    echo $mainContent;
    exit();
}

// --- Layout del Dashboard de Administración ---
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración | Tienda Virtual</title>
    <!-- CSS de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tu archivo CSS personalizado (incluye estilos de admin) -->
    <link rel="stylesheet" href="<?= APP_PATH ?>/assets/css/estilos.css">
</head>
<body class="admin-body">
    <div class="admin-wrapper">
        <!-- Sidebar de Navegación -->
        <aside class="admin-sidebar">
            <h2>Admin Panel</h2>
            <nav>
                <ul>
                    <li><a href="index.php?action=listar" class="<?= ($action === 'listar' || $action === 'registrar' || $action === 'editar') ? 'active' : '' ?>">Productos</a></li>
                    <li><a href="index.php?action=mensajes" class="<?= ($action === 'mensajes') ? 'active' : '' ?>">Mensajes de Contacto</a></li>
                    <li><a href="index.php?action=pedidos" class="<?= ($action === 'pedidos' || $action === 'ver_detalle_pedido' || $action === 'actualizar_estado_pedido') ? 'active' : '' ?>">Pedidos</a></li> <!-- NUEVO: Enlace a Pedidos -->
                    <!-- Puedes añadir más enlaces aquí (ej. Categorías, Usuarios) -->
                    <li><a href="index.php?action=logout">Cerrar Sesión</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Contenido Principal del Panel -->
        <main class="admin-content">
            <?php
            // Mostrar mensajes de sesión (éxito/error)
            if (isset($_SESSION['message'])):
                $alertClass = ($_SESSION['message']['type'] === 'success') ? 'alert-success' : 'alert-danger';
            ?>
                <div class="alert <?= $alertClass ?> alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_SESSION['message']['text']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php
                unset($_SESSION['message']); // Eliminar el mensaje después de mostrarlo
            endif;
            ?>

            <?= $mainContent ?>
        </main>
    </div>

    <!-- JS de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Tu archivo JS personalizado -->
    <script src="<?= APP_PATH ?>/assets/js/funciones.js"></script>
</body>
</html>
