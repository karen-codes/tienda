<?php
// Se define la ruta base de la aplicación si no está definida.
if (!defined('APP_PATH')) {
    define('APP_PATH', dirname(dirname(__FILE__)));
}

// Inicia la sesión si aún no está iniciada. Esto es crucial para el carrito basado en sesiones.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Se incluye el modelo principal (ProductoDAO) y el nuevo MensajeContactoDAO.
require_once APP_PATH . "/app/modelo.php"; 
require_once APP_PATH . "/admin/modelo/MensajeContactoDAO.php"; 

class Controller
{
    /**
     * Muestra la página de inicio con los productos agrupados por categoría.
     */
    public function inicio()
    {
        $dao = new ProductoDAO(); 
        $productos = $dao->obtenerProductosPorCategoria();

        $agrupados = [];
        foreach ($productos as $p) {
            $agrupados[$p['categoria']][] = $p;
        }

        include APP_PATH . "/app/vista/tienda.php";
    }

    /**
     * Muestra la página de detalle de un producto específico.
     * @param int $id El ID del producto a mostrar.
     */
    public function detalle($id)
    {
        if (!$id || !is_numeric($id)) {
            http_response_code(400); 
            echo "ID de producto inválido.";
            return;
        }

        $dao = new ProductoDAO(); 
        $producto = $dao->obtenerPorId($id); 

        if (!$producto) {
            http_response_code(404); 
            echo "Producto no encontrado.";
            return;
        }

        include APP_PATH . "/app/vista/detalle.php";
    }

    /**
     * Muestra la página de contacto y procesa el envío del formulario.
     */
    public function contacto()
    {
        $errors = []; 
        $message = null; 

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            $correo = trim($_POST['correo'] ?? '');
            $asunto = trim($_POST['asunto'] ?? '');
            $mensaje = trim($_POST['mensaje'] ?? '');

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
            } elseif (strlen($mensaje) > 1000) { 
                $errors['mensaje'] = "El mensaje no debe exceder los 1000 caracteres.";
            }

            if (empty($errors)) {
                $mensajeDAO = new MensajesContactoDAO(); 
                if ($mensajeDAO->insertarMensaje($nombre, $correo, $asunto, $mensaje)) {
                    $message = ['type' => 'success', 'text' => '¡Tu mensaje ha sido enviado con éxito!'];
                    $_POST = [];
                } else {
                    $message = ['type' => 'danger', 'text' => 'Hubo un error al enviar tu mensaje. Por favor, inténtalo de nuevo más tarde.'];
                }
            } else {
                $message = ['type' => 'danger', 'text' => 'Por favor, corrige los errores en el formulario.'];
            }
        }

        include APP_PATH . "/app/vista/contacto.php";
    }

    /**
     * Muestra la página del carrito de compras.
     */
    public function carrito()
    {
        // La vista del carrito ya manejará la visualización de $_SESSION['carrito']
        include APP_PATH . "/app/vista/carrito.php";
    }

    /**
     * Añade un producto al carrito de compras.
     * Los datos del producto se obtienen de la base de datos para asegurar su validez.
     */
    public function agregar_al_carrito()
    {
        $id_producto = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
        $cantidad = filter_var($_POST['cantidad'] ?? 1, FILTER_VALIDATE_INT); // Cantidad por defecto 1

        if ($id_producto === false || $id_producto <= 0 || $cantidad === false || $cantidad <= 0) {
            // Manejar error de ID o cantidad inválida
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'Producto o cantidad inválida para añadir al carrito.'];
            header("Location: index.php?action=inicio");
            exit();
        }

        $productoDAO = new ProductoDAO();
        $producto = $productoDAO->obtenerPorId($id_producto); // Asume que este método existe y devuelve los datos del producto

        if (!$producto) {
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'El producto no se encontró.'];
            header("Location: index.php?action=inicio");
            exit();
        }

        // Si el carrito no existe en la sesión, inicializarlo
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }

        // Si el producto ya está en el carrito, actualizar la cantidad
        if (isset($_SESSION['carrito'][$id_producto])) {
            $_SESSION['carrito'][$id_producto]['cantidad'] += $cantidad;
        } else {
            // Si no está, añadir el producto al carrito
            $_SESSION['carrito'][$id_producto] = [
                'id' => $producto['id'],
                'nombre' => $producto['nombre'],
                'precio' => $producto['precio'],
                'foto' => $producto['foto'], // Se asume que la foto se obtiene con obtenerPorId
                'cantidad' => $cantidad
            ];
        }

        $_SESSION['message'] = ['type' => 'success', 'text' => htmlspecialchars($producto['nombre']) . ' añadido al carrito.'];
        header("Location: index.php?action=carrito"); // Redirigir al carrito o a la página anterior
        exit();
    }

    /**
     * Actualiza las cantidades de los productos en el carrito.
     */
    public function actualizar_carrito()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cantidad'])) {
            foreach ($_POST['cantidad'] as $id_producto => $cantidad) {
                $id_producto = filter_var($id_producto, FILTER_VALIDATE_INT);
                $cantidad = filter_var($cantidad, FILTER_VALIDATE_INT);

                if ($id_producto !== false && $id_producto > 0 && $cantidad !== false && $cantidad > 0) {
                    if (isset($_SESSION['carrito'][$id_producto])) {
                        $_SESSION['carrito'][$id_producto]['cantidad'] = $cantidad;
                    }
                } elseif ($id_producto !== false && $id_producto > 0 && $cantidad !== false && $cantidad <= 0) {
                    // Si la cantidad es 0 o menos, eliminar el producto del carrito
                    unset($_SESSION['carrito'][$id_producto]);
                }
            }
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Carrito actualizado con éxito.'];
        } else {
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'No se recibieron datos para actualizar el carrito.'];
        }
        header("Location: index.php?action=carrito");
        exit();
    }

    /**
     * Elimina un producto específico del carrito.
     */
    public function eliminar_del_carrito()
    {
        $id_producto = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);

        if ($id_producto !== false && $id_producto > 0) {
            if (isset($_SESSION['carrito'][$id_producto])) {
                unset($_SESSION['carrito'][$id_producto]);
                $_SESSION['message'] = ['type' => 'success', 'text' => 'Producto eliminado del carrito.'];
            } else {
                $_SESSION['message'] = ['type' => 'danger', 'text' => 'El producto no se encontró en el carrito.'];
            }
        } else {
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'ID de producto inválido para eliminar del carrito.'];
        }
        header("Location: index.php?action=carrito");
        exit();
    }

    /**
     * Vacía todo el carrito de compras.
     */
    public function vaciar_carrito()
    {
        unset($_SESSION['carrito']);
        $_SESSION['message'] = ['type' => 'success', 'text' => 'El carrito ha sido vaciado.'];
        header("Location: index.php?action=carrito");
        exit();
    }

    /**
     * Lógica para finalizar la compra (simulada).
     * En una aplicación real, esto implicaría procesar el pago, crear un pedido, etc.
     */
    public function finalizar_compra()
    {
        if (empty($_SESSION['carrito'])) {
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'No puedes finalizar la compra con un carrito vacío.'];
            header("Location: index.php?action=carrito");
            exit();
        }

        // Aquí iría la lógica real de procesamiento de pago y creación de pedido.
        // Por ahora, solo simulamos y vaciamos el carrito.
        $_SESSION['message'] = ['type' => 'success', 'text' => '¡Compra finalizada con éxito! Gracias por tu pedido.'];
        unset($_SESSION['carrito']); // Vaciar el carrito después de la compra
        header("Location: index.php?action=inicio"); // Redirigir a la página de inicio o a una página de confirmación
        exit();
    }
}
