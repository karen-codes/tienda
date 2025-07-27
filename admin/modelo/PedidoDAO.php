<?php
// Se define la ruta base de la aplicación si no está definida.
if (!defined('APP_PATH')) {
    define('APP_PATH', dirname(dirname(dirname(__FILE__))));
}

// Se incluye el archivo de conexión a la base de datos.
require_once APP_PATH . "/config/conexion.php";

class PedidoDAO
{
    private $conexion;

    public function __construct()
    {
        // Establece la conexión a la base de datos al instanciar la clase.
        $this->conexion = Conexion::conectar();
    }

    /**
     * Guarda un nuevo pedido y sus detalles en la base de datos.
     * Utiliza una transacción para asegurar la atomicidad de la operación.
     *
     * @param string $nombreCliente Nombre del cliente.
     * @param string $correoCliente Correo electrónico del cliente.
     * @param string $direccionEnvio Dirección de envío del pedido.
     * @param float $total Total del pedido.
     * @param array $itemsCarrito Array de productos en el carrito (id, nombre, precio, cantidad).
     * @return bool True si el pedido se guardó correctamente, false en caso contrario.
     */
    public function guardarPedido($nombreCliente, $correoCliente, $direccionEnvio, $total, $itemsCarrito)
    {
        $this->conexion->beginTransaction(); // Inicia la transacción

        try {
            // 1. Insertar el pedido en la tabla 'pedidos'
            $sqlPedido = "INSERT INTO pedidos (nombre_cliente, correo_cliente, direccion_envio, total) VALUES (?, ?, ?, ?)";
            $stmtPedido = $this->conexion->prepare($sqlPedido);
            $stmtPedido->execute([$nombreCliente, $correoCliente, $direccionEnvio, $total]);

            // Obtener el ID del pedido recién insertado
            $pedidoId = $this->conexion->lastInsertId();

            // 2. Insertar los detalles del pedido en la tabla 'detalle_pedido'
            $sqlDetalle = "INSERT INTO detalle_pedido (pedido_id, producto_id, nombre_producto, precio_unitario, cantidad, subtotal) VALUES (?, ?, ?, ?, ?, ?)";
            $stmtDetalle = $this->conexion->prepare($sqlDetalle);

            foreach ($itemsCarrito as $item) {
                $productoId = $item['id'];
                $nombreProducto = $item['nombre'];
                $precioUnitario = $item['precio'];
                $cantidad = $item['cantidad'];
                $subtotal = $precioUnitario * $cantidad;

                $stmtDetalle->execute([$pedidoId, $productoId, $nombreProducto, $precioUnitario, $cantidad, $subtotal]);
            }

            $this->conexion->commit(); // Confirma la transacción si todo fue exitoso
            return true;

        } catch (PDOException $e) {
            $this->conexion->rollBack(); // Revierte la transacción en caso de error
            error_log("Error al guardar el pedido: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene todos los pedidos de la base de datos.
     *
     * @return array Un array de pedidos.
     */
    public function obtenerPedidos()
    {
        $sql = "SELECT id, nombre_cliente, correo_cliente, total, fecha_pedido, estado FROM pedidos ORDER BY fecha_pedido DESC";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene los detalles de un pedido específico.
     *
     * @param int $pedidoId El ID del pedido.
     * @return array Un array con los detalles del pedido.
     */
    public function obtenerDetallePedido($pedidoId)
    {
        $sql = "SELECT dp.nombre_producto, dp.precio_unitario, dp.cantidad, dp.subtotal, p.nombre_cliente, p.correo_cliente, p.direccion_envio, p.total, p.fecha_pedido, p.estado
                FROM detalle_pedido dp
                JOIN pedidos p ON dp.pedido_id = p.id
                WHERE dp.pedido_id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$pedidoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Actualiza el estado de un pedido.
     *
     * @param int $pedidoId El ID del pedido.
     * @param string $nuevoEstado El nuevo estado del pedido.
     * @return bool True si la actualización fue exitosa, false en caso contrario.
     */
    public function actualizarEstadoPedido($pedidoId, $nuevoEstado)
    {
        $sql = "UPDATE pedidos SET estado = ? WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        try {
            return $stmt->execute([$nuevoEstado, $pedidoId]);
        } catch (PDOException $e) {
            error_log("Error al actualizar estado del pedido: " . $e->getMessage());
            return false;
        }
    }
}
