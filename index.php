<?php
// Se define la ruta base de la aplicación si no está definida.
// Esto asegura que las rutas relativas funcionen correctamente desde cualquier script.
if (!defined('APP_PATH')) {
    define('APP_PATH', dirname(__FILE__)); // APP_PATH debe apuntar a la raíz de 'tienda'
}

// Inicia la sesión si aún no está iniciada.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Se incluye el controlador público que manejará las acciones de la tienda.
require_once APP_PATH . '/app/controlador.php';

// Se incluye la utilidad CSRF.
// CORRECCIÓN: La ruta correcta es APP_PATH . "/config/csrf.php"
require_once APP_PATH . "/config/csrf.php"; 


// Se crea una instancia del controlador.
$controller = new Controller();

// Se obtiene la acción solicitada de la URL.
// Si no se especifica ninguna acción, por defecto será 'inicio'.
$action = $_GET['action'] ?? 'inicio';

// Se manejan las diferentes acciones posibles.
switch ($action) {
    case 'inicio':
        // Muestra la página principal de la tienda.
        $controller->inicio();
        break;
    case 'detalle':
        // Muestra los detalles de un producto específico.
        $id = $_GET['id'] ?? null;
        $controller->detalle($id);
        break;
    case 'contacto':
        // Muestra la página de contacto y procesa el formulario.
        $controller->contacto();
        break;
    case 'carrito':
        // Muestra la página del carrito de compras.
        $controller->carrito();
        break;
    case 'agregar_al_carrito':
        $controller->agregar_al_carrito();
        break;
    case 'actualizar_carrito':
        $controller->actualizar_carrito();
        break;
    case 'eliminar_del_carrito':
        $controller->eliminar_del_carrito();
        break;
    case 'vaciar_carrito':
        $controller->vaciar_carrito();
        break;
    case 'finalizar_compra':
        $controller->finalizar_compra();
        break;
    default:
        // Si la acción no es reconocida, se muestra un error 404.
        http_response_code(404); // Not Found
        echo "Página no encontrada.";
        break;
}
