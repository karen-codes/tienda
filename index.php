<?php
// Se define la ruta base de la aplicación.
if (!defined('APP_PATH')) {
    define('APP_PATH', dirname(__FILE__)); // APP_PATH es la raíz de 'tienda'
}

// Inicia la sesión si aún no está iniciada.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Se incluye el controlador público que manejará las acciones de la tienda.
require_once APP_PATH . '/app/controlador.php';

// Se incluye la utilidad CSRF. (Aunque no se usa directamente en el frontend, es buena práctica tenerla cargada si se usa en algún formulario futuro)
require_once APP_PATH . "/config/csrf.php"; 


// Se crea una instancia del controlador.
$controller = new Controller();

// --- Lógica para URLs Amigables ---
// Obtiene la ruta reescrita del parámetro 'path'
$requestPath = $_GET['path'] ?? '';

// Divide la ruta en segmentos
$segments = explode('/', trim($requestPath, '/'));

// Determina la acción y los parámetros
$action = 'inicio'; // Acción por defecto
$id = null; // ID por defecto

if (!empty($segments[0])) {
    $action = $segments[0]; // El primer segmento es la acción

    if (isset($segments[1]) && is_numeric($segments[1])) {
        $id = (int)$segments[1]; // El segundo segmento podría ser un ID
    }
}
// --- Fin Lógica para URLs Amigables ---


// Se manejan las diferentes acciones posibles.
switch ($action) {
    case 'inicio':
        $controller->inicio();
        break;
    case 'detalle': // Ahora se accederá como /detalle/ID
        $controller->detalle($id);
        break;
    case 'contacto':
        $controller->contacto();
        break;
    case 'carrito':
        $controller->carrito();
        break;
    case 'agregar_al_carrito': // Se seguirá usando con GET para el ID, POST para cantidad
        $controller->agregar_al_carrito();
        break;
    case 'actualizar_carrito':
        $controller->actualizar_carrito();
        break;
    case 'eliminar_del_carrito': // Se seguirá usando con GET para el ID
        $controller->eliminar_del_carrito();
        break;
    case 'vaciar_carrito':
        $controller->vaciar_carrito();
        break;
    case 'finalizar_compra':
        $controller->finalizar_compra();
        break;
    case 'procesar_compra':
        $controller->procesar_compra();
        break;
    default:
        // Si la acción no es reconocida, se muestra un error 404.
        http_response_code(404); // Not Found
        echo "Página no encontrada.";
        break;
}

