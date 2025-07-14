<?php
require_once "app/controlador.php";

$action = $_GET['action'] ?? 'inicio';

$controller = new Controller(); 

switch ($action) {
    case 'inicio':
        $controller->inicio();
        break;
    case 'detalle':
        $id = $_GET['id'] ?? null;
        $controller->detalle($id);
        break;
    default:
        http_response_code(404);
        echo "Página no encontrada.";
        break;
}