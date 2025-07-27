<?php
// Se define la ruta base de la aplicación si no está definida.
if (!defined('APP_PATH')) {
    define('APP_PATH', dirname(dirname(dirname(__FILE__))));
}

// Incluye el DAO de mensajes de contacto.
require_once APP_PATH . "/admin/modelo/MensajeContactoDAO.php"; 

// CORRECCIÓN: El nombre de la clase ahora es 'MensajeController' (singular)
class MensajeController 
{
    private $mensajeDAO;

    public function __construct()
    {
        $this->mensajeDAO = new MensajeContactoDAO();
    }

    /**
     * Lista todos los mensajes de contacto.
     */
    public function listarMensajes()
    {
        $mensajes = $this->mensajeDAO->listarMensajes(); // Llama al método en el DAO
        // La vista para mostrar los mensajes es 'mensajes.php' (en plural)
        include APP_PATH . "/admin/vista/mensajes.php"; 
    }

    // En el futuro, podrías añadir métodos para ver un mensaje individual, marcar como leído, eliminar, etc.
}
