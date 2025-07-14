<?php
require_once "controlador/ProductoController.php";

$controller = new ProductoController();

$action = $_GET['action'] ?? 'registrar';

if ($action === 'listar') {
    $controller->listar();
} else {
    $controller->registrar();
}
