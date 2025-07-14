<?php
require_once __DIR__ . "/../config/conexion.php";

class ProductoDAO
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = Conexion::conectar();
    }

    public function obtenerProductosPorCategoria()
    {
        $sql = "SELECT p.*, c.nombre AS categoria 
                FROM productos p 
                JOIN categoria c ON p.categoria_id = c.id 
                ORDER BY c.nombre, p.nombre";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id)
    {
        $sql = "SELECT p.*, c.nombre AS categoria
            FROM productos p
            INNER JOIN categoria c ON p.categoria_id = c.id
            WHERE p.id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
