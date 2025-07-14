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
}