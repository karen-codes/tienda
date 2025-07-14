<?php
require_once "config/conexion.php";

class ProductoDAO
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = Conexion::conectar();
    }

    public function insertar($nombre, $precio, $foto, $categoria_id)
    {
        $sql = "INSERT INTO productos (nombre, precio, foto, categoria_id) VALUES (?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([$nombre, $precio, $foto, $categoria_id]);
    }


    public function listar()
    {
        $sql = "SELECT p.id, p.nombre, p.precio, p.foto, c.nombre AS categoria
            FROM productos p
            INNER JOIN categoria c ON p.categoria_id = c.id
            ORDER BY p.id DESC";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function eliminar($id)
    {
        // Obtener la foto antes de eliminar
        $stmt = $this->conexion->prepare("SELECT foto FROM productos WHERE id = ?");
        $stmt->execute([$id]);
        $foto = $stmt->fetchColumn();

        // Eliminar el registro
        $stmt = $this->conexion->prepare("DELETE FROM productos WHERE id = ?");
        $resultado = $stmt->execute([$id]);

        // Si se eliminó correctamente, elimina la imagen
        if ($resultado && $foto && file_exists("imagenes/$foto")) {
            unlink("imagenes/$foto");
        }

        return $resultado;
    }

    public function buscarPorId($id)
    {
        $sql = "SELECT * FROM productos WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizar($id, $nombre, $precio, $foto, $categoria_id)
    {
        if ($foto) {
            $sql = "UPDATE productos SET nombre = ?, precio = ?, foto = ?, categoria_id = ? WHERE id = ?";
            $stmt = $this->conexion->prepare($sql);
            return $stmt->execute([$nombre, $precio, $foto, $categoria_id, $id]);
        } else {
            $sql = "UPDATE productos SET nombre = ?, precio = ?, categoria_id = ? WHERE id = ?";
            $stmt = $this->conexion->prepare($sql);
            return $stmt->execute([$nombre, $precio, $categoria_id, $id]);
        }
    }

}