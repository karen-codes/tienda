<?php
// Se define la ruta base de la aplicación si no está definida.
// Esto es crucial para que los 'require_once' funcionen correctamente.
if (!defined('APP_PATH')) {
    define('APP_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once APP_PATH . "/config/conexion.php";

class ProductoDAO
{
    private $conexion;

    public function __construct()
    {
        // Establece la conexión a la base de datos al instanciar la clase.
        $this->conexion = Conexion::conectar();
    }

    /**
     * Inserta un nuevo producto en la base de datos.
     *
     * @param string $nombre El nombre del producto.
     * @param float $precio El precio del producto.
     * @param string|null $foto El nombre del archivo de la foto del producto (o null si no hay).
     * @param string $descripcion La descripción detallada del producto.
     * @param int $categoria_id El ID de la categoría a la que pertenece el producto.
     * @return bool True si la inserción fue exitosa, false en caso contrario.
     */
    public function insertar($nombre, $precio, $foto, $descripcion, $categoria_id) // Agregado $descripcion
    {
        $sql = "INSERT INTO productos (nombre, precio, foto, descripcion, categoria_id) VALUES (?, ?, ?, ?, ?)"; // SQL actualizado
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([$nombre, $precio, $foto, $descripcion, $categoria_id]); // Parámetros actualizados
    }

    /**
     * Obtiene una lista de todos los productos, incluyendo el nombre de su categoría.
     *
     * @return array Un array de productos.
     */
    public function listar()
    {
        $sql = "SELECT p.id, p.nombre, p.precio, p.foto, p.descripcion, c.nombre AS categoria
                FROM productos p
                INNER JOIN categoria c ON p.categoria_id = c.id
                ORDER BY p.id DESC";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Elimina un producto de la base de datos por su ID.
     * También elimina el archivo de imagen asociado si existe.
     *
     * @param int $id El ID del producto a eliminar.
     * @return bool True si la eliminación fue exitosa, false en caso contrario.
     */
    public function eliminar($id)
    {
        // Obtener el nombre de la foto antes de eliminar el registro.
        $stmt = $this->conexion->prepare("SELECT foto FROM productos WHERE id = ?");
        $stmt->execute([$id]);
        $foto = $stmt->fetchColumn();

        // Eliminar el registro de la base de datos.
        $stmt = $this->conexion->prepare("DELETE FROM productos WHERE id = ?");
        $resultado = $stmt->execute([$id]);

        // Si el registro se eliminó correctamente y hay una foto asociada, eliminar el archivo de imagen.
        if ($resultado && $foto && file_exists(APP_PATH . "/imagenes/" . $foto)) {
            unlink(APP_PATH . "/imagenes/" . $foto);
        }

        return $resultado;
    }

    /**
     * Busca un producto en la base de datos por su ID.
     *
     * @param int $id El ID del producto a buscar.
     * @return array|false Un array asociativo con los datos del producto o false si no se encuentra.
     */
    public function buscarPorId($id)
    {
        // La consulta SELECT * ya incluye la columna 'descripcion' automáticamente.
        $sql = "SELECT * FROM productos WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Actualiza un producto existente en la base de datos.
     *
     * @param int $id El ID del producto a actualizar.
     * @param string $nombre El nuevo nombre del producto.
     * @param float $precio El nuevo precio del producto.
     * @param string|null $foto El nuevo nombre del archivo de la foto (o null si no se cambia).
     * @param string $descripcion La nueva descripción del producto.
     * @param int $categoria_id El nuevo ID de la categoría.
     * @return bool True si la actualización fue exitosa, false en caso contrario.
     */
    public function actualizar($id, $nombre, $precio, $foto, $descripcion, $categoria_id) // Agregado $descripcion
    {
        try {
            if ($foto) {
                // Obtener el nombre de la imagen anterior para eliminarla.
                $stmt = $this->conexion->prepare("SELECT foto FROM productos WHERE id = ?");
                $stmt->execute([$id]);
                $fotoAnterior = $stmt->fetchColumn();

                // Actualizar el producto incluyendo la nueva imagen y la descripción.
                $sql = "UPDATE productos SET nombre = ?, precio = ?, foto = ?, descripcion = ?, categoria_id = ? WHERE id = ?"; // SQL actualizado
                $stmt = $this->conexion->prepare($sql);
                $resultado = $stmt->execute([$nombre, $precio, $foto, $descripcion, $categoria_id, $id]); // Parámetros actualizados

                // Eliminar la imagen antigua del servidor si existe y la actualización fue exitosa.
                if ($resultado && $fotoAnterior && file_exists(APP_PATH . "/imagenes/" . $fotoAnterior)) {
                    unlink(APP_PATH . "/imagenes/" . $fotoAnterior);
                }

                return $resultado;

            } else {
                // Actualizar el producto sin cambiar la imagen, pero incluyendo la descripción.
                $sql = "UPDATE productos SET nombre = ?, precio = ?, descripcion = ?, categoria_id = ? WHERE id = ?"; // SQL actualizado
                $stmt = $this->conexion->prepare($sql);
                return $stmt->execute([$nombre, $precio, $descripcion, $categoria_id, $id]); // Parámetros actualizados
            }

        } catch (PDOException $e) {
            // En un entorno de producción, aquí se registraría el error en lugar de solo devolver false.
            error_log("Error al actualizar producto: " . $e->getMessage());
            return false;
        }
    }
}