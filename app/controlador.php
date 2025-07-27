<?php
// Se define la ruta base de la aplicación si no está definida.
// Esto asegura que las rutas relativas funcionen correctamente desde cualquier script.
if (!defined('APP_PATH')) {
    define('APP_PATH', dirname(dirname(__FILE__)));
}

// Se incluye el modelo principal (ProductoDAO), asumiendo que está definido en app/modelo.php.
require_once APP_PATH . "/app/modelo.php"; 

// Se incluye el DAO para los mensajes de contacto.
require_once APP_PATH . "/admin/modelo/MensajeContactoDAO.php"; 

class Controller
{
    /**
     * Muestra la página de inicio con los productos agrupados por categoría.
     */
    public function inicio()
    {
        // Se asume que ProductoDAO es parte de modelo.php o está en app/modelo.
        $dao = new ProductoDAO(); 
        $productos = $dao->obtenerProductosPorCategoria();

        // Agrupar productos por categoría para la vista.
        $agrupados = [];
        foreach ($productos as $p) {
            $agrupados[$p['categoria']][] = $p;
        }

        // Se incluye la vista principal de la tienda.
        include APP_PATH . "/app/vista/tienda.php";
    }

    /**
     * Muestra la página de detalle de un producto específico.
     * @param int $id El ID del producto a mostrar.
     */
    public function detalle($id)
    {
        // Validación básica del ID del producto.
        if (!$id || !is_numeric($id)) {
            http_response_code(400); // Bad Request
            echo "ID de producto inválido.";
            return;
        }

        // Se asume que ProductoDAO es parte de modelo.php o está en app/modelo.
        $dao = new ProductoDAO(); 
        // Se asume que este método obtiene la descripción también.
        $producto = $dao->obtenerPorId($id); 

        // Si el producto no se encuentra, se muestra un error 404.
        if (!$producto) {
            http_response_code(404); // Not Found
            echo "Producto no encontrado.";
            return;
        }

        // Se incluye la vista de detalle del producto.
        include APP_PATH . "/app/vista/detalle.php";
    }

    /**
     * Muestra la página de contacto y procesa el envío del formulario.
     */
    public function contacto()
    {
        $errors = []; // Para almacenar errores de validación
        $message = null; // Para mensajes de éxito/error general

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 1. Recoger y sanear los datos del formulario
            $nombre = trim($_POST['nombre'] ?? '');
            $correo = trim($_POST['correo'] ?? '');
            $asunto = trim($_POST['asunto'] ?? '');
            $mensaje = trim($_POST['mensaje'] ?? '');

            // 2. Validación del lado del servidor
            if (empty($nombre)) {
                $errors['nombre'] = "El nombre es obligatorio.";
            } elseif (strlen($nombre) > 100) {
                $errors['nombre'] = "El nombre no debe exceder los 100 caracteres.";
            }

            if (empty($correo)) {
                $errors['correo'] = "El correo electrónico es obligatorio.";
            } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                $errors['correo'] = "El formato del correo electrónico no es válido.";
            } elseif (strlen($correo) > 100) {
                $errors['correo'] = "El correo no debe exceder los 100 caracteres.";
            }

            if (strlen($asunto) > 255) {
                $errors['asunto'] = "El asunto no debe exceder los 255 caracteres.";
            }

            if (empty($mensaje)) {
                $errors['mensaje'] = "El mensaje es obligatorio.";
            } elseif (strlen($mensaje) > 1000) { // Límite de 1000 caracteres para el mensaje
                $errors['mensaje'] = "El mensaje no debe exceder los 1000 caracteres.";
            }

            // 3. Si no hay errores, guardar en la base de datos
            if (empty($errors)) {
                $mensajeDAO = new MensajeContactoDAO();
                if ($mensajeDAO->insertarMensaje($nombre, $correo, $asunto, $mensaje)) {
                    $message = ['type' => 'success', 'text' => '¡Tu mensaje ha sido enviado con éxito!'];
                    // Opcional: Limpiar los campos POST para que el formulario aparezca vacío
                    $_POST = [];
                } else {
                    $message = ['type' => 'danger', 'text' => 'Hubo un error al enviar tu mensaje. Por favor, inténtalo de nuevo más tarde.'];
                }
            } else {
                // Si hay errores, se mostrarán en la vista, y los campos se repoblarán
                $message = ['type' => 'danger', 'text' => 'Por favor, corrige los errores en el formulario.'];
            }
        }

        // Se incluye la vista del formulario de contacto.
        include APP_PATH . "/app/vista/contacto.php";
    }

    /**
     * Muestra la página del carrito de compras.
     * Aquí se podría cargar los ítems del carrito desde la sesión.
     */
    public function carrito()
    {
        // Lógica para la página del carrito (ej. obtener ítems del carrito de la sesión).
        // Por ahora, solo se incluye la vista.
        include APP_PATH . "/app/vista/carrito.php";
    }

    // Puedes añadir más métodos para otras acciones aquí.
}
