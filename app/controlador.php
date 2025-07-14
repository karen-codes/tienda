<?php
require_once "modelo.php";

$dao = new ProductoDAO();
$productos = $dao->obtenerProductosPorCategoria();

// Agrupar productos por categoría
$agrupados = [];
foreach ($productos as $p) {
    $agrupados[$p['categoria']][] = $p;
}

// Mostrar la vista
require_once "vista/tienda.php";
