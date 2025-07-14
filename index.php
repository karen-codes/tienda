<?php
require_once "controlador/ProductoController.php";

$controller = new ProductoController();

$action = $_GET['action'] ?? 'registrar';

switch ($action) {
    case 'listar':
        $controller->listar();
        break;
    case 'registrar':
        $controller->registrar();
        break;
    case 'eliminar':
        $controller->eliminar(); 
        break;
    case 'editar':
        $controller->editar();
        break;
    // ...
    default:
        $controller->listar();
        break;
}
