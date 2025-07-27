<?php
// Define la ruta base de la aplicación.
// Para este controlador, APP_PATH debe apuntar a la raíz de 'tienda'.
if (!defined('APP_PATH')) {
    define('APP_PATH', dirname(dirname(dirname(__FILE__))));
}

// Incluye el DAO de Pedidos.
require_once APP_PATH . "/admin/modelo/PedidoDAO.php"; 

class PedidoController
{
    private $pedidoDAO;

    public function __construct()
    {
        $this->pedidoDAO = new PedidoDAO();
    }

    /**
     * Lista todos los pedidos registrados en la base de datos.
     */
    public function listarPedidos()
    {
        $pedidos = $this->pedidoDAO->obtenerPedidos(); // Obtiene todos los pedidos
        include APP_PATH . "/admin/vista/pedidos.php"; // Incluye la vista para mostrar los pedidos
    }

    /**
     * Muestra los detalles de un pedido específico.
     * @param int $id El ID del pedido a mostrar.
     */
    public function verDetallePedido($id)
    {
        $id = filter_var($id, FILTER_VALIDATE_INT);

        if ($id === false || $id <= 0) {
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'ID de pedido inválido.'];
            header("Location: index.php?action=pedidos"); // Redirige a la lista de pedidos
            exit();
        }

        $pedidoDetalles = $this->pedidoDAO->obtenerDetallePedido($id);

        if (empty($pedidoDetalles)) {
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'Pedido no encontrado o sin detalles.'];
            header("Location: index.php?action=pedidos");
            exit();
        }

        // El primer elemento del array de detalles contiene la información general del pedido
        $pedidoInfo = [
            'nombre_cliente' => $pedidoDetalles[0]['nombre_cliente'],
            'correo_cliente' => $pedidoDetalles[0]['correo_cliente'],
            'direccion_envio' => $pedidoDetalles[0]['direccion_envio'],
            'total' => $pedidoDetalles[0]['total'],
            'fecha_pedido' => $pedidoDetalles[0]['fecha_pedido'],
            'estado' => $pedidoDetalles[0]['estado'],
            'id' => $id // Aseguramos que el ID del pedido esté disponible
        ];

        include APP_PATH . "/admin/vista/detalle_pedido.php"; // NUEVO: Vista para el detalle del pedido
    }

    /**
     * Actualiza el estado de un pedido.
     * Recibe el ID del pedido y el nuevo estado.
     */
    public function actualizarEstado()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
            $estado = trim($_POST['estado'] ?? '');

            if ($id === false || $id <= 0 || empty($estado)) {
                $_SESSION['message'] = ['type' => 'danger', 'text' => 'Datos inválidos para actualizar el estado del pedido.'];
                header("Location: index.php?action=pedidos");
                exit();
            }

            if ($this->pedidoDAO->actualizarEstadoPedido($id, $estado)) {
                $_SESSION['message'] = ['type' => 'success', 'text' => 'Estado del pedido actualizado con éxito.'];
            } else {
                $_SESSION['message'] = ['type' => 'danger', 'text' => 'Error al actualizar el estado del pedido.'];
            }
        } else {
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'Método no permitido para actualizar el estado del pedido.'];
        }
        header("Location: index.php?action=pedidos");
        exit();
    }
}
