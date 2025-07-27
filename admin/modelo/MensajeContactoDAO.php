<?php
// Se define la ruta base de la aplicación si no está definida.
if (!defined('APP_PATH')) {
    define('APP_PATH', dirname(dirname(dirname(__FILE__))));
}

// Se incluye el archivo de conexión a la base de datos.
require_once APP_PATH . "/config/conexion.php";

// CORRECCIÓN: El nombre de la clase ahora es 'MensajeContactoDAO' (singular)
class MensajeContactoDAO 
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = Conexion::conectar();
    }

    /**
     * Inserta un nuevo mensaje de contacto en la base de datos.
     *
     * @param string $nombre El nombre del remitente.
     * @param string $correo El correo electrónico del remitente.
     * @param string $asunto El asunto del mensaje.
     * @param string $mensaje El contenido del mensaje.
     * @return bool True si la inserción fue exitosa, false en caso contrario.
     */
    public function insertarMensaje($nombre, $correo, $asunto, $mensaje)
    {
        $sql = "INSERT INTO mensajes_contacto (nombre, correo, asunto, mensaje) VALUES (?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        try {
            return $stmt->execute([$nombre, $correo, $asunto, $mensaje]);
        } catch (PDOException $e) {
            error_log("Error al insertar mensaje de contacto: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lista todos los mensajes de contacto de la base de datos.
     *
     * @return array Un array de mensajes de contacto.
     */
    public function listarMensajes()
    {
        $sql = "SELECT id, nombre, correo, asunto, mensaje, fecha_envio FROM mensajes_contacto ORDER BY fecha_envio DESC";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
