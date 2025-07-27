<?php

// Se define la ruta base de la aplicación si no está definida (buena práctica).
// Esto ayuda a que los 'require_once' funcionen correctamente sin importar dónde se ejecute el script.
if (!defined('APP_PATH')) {
    define('APP_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once APP_PATH . "/admin/modelo/ProductoDAO.php";
require_once APP_PATH . "/config/conexion.php"; // Asegurarse de que la conexión esté disponible si se necesita en el controlador

class ProductoController
{
    private $modeloProducto; // Cambiado a modeloProducto para mayor claridad
    private $modeloCategoria; // Añadido para el manejo de categorías

    public function __construct()
    {
        $this->modeloProducto = new ProductoDAO();
        // Se instancia CategoriaDAO aquí si es usada en múltiples métodos
        // o se instancia en cada método donde se necesite.
    }

    /**
     * Muestra el formulario de registro de producto y procesa su envío.
     * Incluye validación del lado del servidor.
     */
    public function registrar()
    {
        // Se requiere el DAO de Categoría para obtener las opciones del select.
        require_once APP_PATH . "/admin/modelo/CategoriaDAO.php";
        $this->modeloCategoria = new CategoriaDAO();
        $categorias = $this->modeloCategoria->listar();

        $errors = []; // Array para almacenar los errores de validación
        $productoData = []; // Para mantener los datos del formulario si hay errores

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Recoger y sanear los datos del formulario
            $nombre = trim($_POST['nombre'] ?? '');
            $precio = filter_var($_POST['precio'] ?? '', FILTER_VALIDATE_FLOAT); // Valida y convierte a float
            $categoria_id = filter_var($_POST['categoria_id'] ?? '', FILTER_VALIDATE_INT); // Valida y convierte a int
            $descripcion = trim($_POST['descripcion'] ?? ''); // Nuevo campo

            // Guardar los datos en $productoData para repoblar el formulario en caso de error
            $productoData = [
                'nombre' => $nombre,
                'precio' => $_POST['precio'] ?? '', // Mantener el string original para el input type="number"
                'categoria_id' => $_POST['categoria_id'] ?? '',
                'descripcion' => $descripcion,
                'foto' => null // La foto se maneja aparte
            ];

            // --- Validación del lado del servidor ---

            if (empty($nombre)) {
                $errors['nombre'] = "El nombre del producto es obligatorio.";
            } elseif (strlen($nombre) > 100) {
                $errors['nombre'] = "El nombre no debe exceder los 100 caracteres.";
            }

            if ($precio === false || $precio <= 0) {
                $errors['precio'] = "El precio debe ser un número válido y mayor que cero.";
            }

            if ($categoria_id === false || $categoria_id <= 0) {
                $errors['categoria_id'] = "Debe seleccionar una categoría válida.";
            } else {
                // Opcional: Verificar si la categoría existe en la base de datos
                // $catExiste = $this->modeloCategoria->buscarPorId($categoria_id);
                // if (!$catExiste) {
                //     $errors['categoria_id'] = "La categoría seleccionada no existe.";
                // }
            }

            if (strlen($descripcion) > 500) { // Límite de 500 caracteres para la descripción
                $errors['descripcion'] = "La descripción no debe exceder los 500 caracteres.";
            }

            // Manejo y validación de la foto
            $foto = null;
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['foto']['tmp_name'];
                $fileName = $_FILES['foto']['name'];
                $fileSize = $_FILES['foto']['size'];
                $fileType = $_FILES['foto']['type'];
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));

                $allowedfileExtensions = ['jpg', 'gif', 'png', 'jpeg'];
                if (!in_array($fileExtension, $allowedfileExtensions)) {
                    $errors['foto'] = "Tipo de archivo de imagen no permitido. Solo JPG, JPEG, PNG, GIF.";
                } elseif ($fileSize > 5000000) { // Límite de 5MB
                    $errors['foto'] = "El tamaño de la imagen no debe exceder los 5MB.";
                } else {
                    $nombreArchivo = uniqid() . "." . $fileExtension; // Nombre único con extensión
                    $rutaDestino = APP_PATH . "/imagenes/" . $nombreArchivo; // Ruta absoluta

                    if (!move_uploaded_file($fileTmpPath, $rutaDestino)) {
                        $errors['foto'] = "Error al subir la imagen.";
                    } else {
                        $foto = $nombreArchivo;
                    }
                }
            } elseif (isset($_FILES['foto']) && $_FILES['foto']['error'] != UPLOAD_ERR_NO_FILE) {
                 // Error de subida que no es "no se seleccionó archivo"
                $errors['foto'] = "Error en la subida del archivo: " . $_FILES['foto']['error'];
            }

            // Si no hay errores, proceder con la inserción
            if (empty($errors)) {
                // Se asume que el método insertar en ProductoDAO.php acepta 'descripcion'
                $this->modeloProducto->insertar($nombre, $precio, $foto, $descripcion, $categoria_id);
                // Opcional: Añadir un mensaje de éxito a la sesión para mostrar en la lista
                // $_SESSION['message'] = ['type' => 'success', 'text' => 'Producto registrado con éxito!'];
                header("Location: index.php?action=listar");
                exit();
            }
        }

        // Si es GET o hay errores POST, mostrar el formulario
        // Se pasan los errores y los datos del formulario a la vista
        include APP_PATH . "/admin/vista/formulario.php";
    }

    /**
     * Muestra la lista de productos.
     */
    public function listar()
    {
        $productos = $this->modeloProducto->listar();
        include APP_PATH . "/admin/vista/lista.php";
    }

    /**
     * Elimina un producto por su ID.
     */
    public function eliminar()
    {
        if (isset($_GET['id'])) {
            $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
            if ($id !== false && $id > 0) {
                // Opcional: Obtener el nombre de la foto para eliminarla del servidor
                $producto = $this->modeloProducto->buscarPorId($id);
                if ($producto && !empty($producto['foto'])) {
                    $rutaFoto = APP_PATH . "/imagenes/" . $producto['foto'];
                    if (file_exists($rutaFoto)) {
                        unlink($rutaFoto); // Eliminar el archivo de imagen
                    }
                }
                $this->modeloProducto->eliminar($id);
                // Opcional: Mensaje de éxito
                // $_SESSION['message'] = ['type' => 'success', 'text' => 'Producto eliminado con éxito!'];
            } else {
                // Opcional: Mensaje de error si el ID es inválido
                // $_SESSION['message'] = ['type' => 'danger', 'text' => 'ID de producto inválido para eliminar.'];
            }
        }
        header("Location: index.php?action=listar");
        exit();
    }

    /**
     * Muestra el formulario de edición de producto y procesa su envío.
     * Incluye validación del lado del servidor.
     */
    public function editar()
    {
        require_once APP_PATH . "/admin/modelo/CategoriaDAO.php";
        $this->modeloCategoria = new CategoriaDAO();
        $categorias = $this->modeloCategoria->listar();

        $errors = [];
        $producto = null; // Variable para los datos del producto a editar

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Recoger y sanear los datos del formulario POST
            $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
            $nombre = trim($_POST['nombre'] ?? '');
            $precio = filter_var($_POST['precio'] ?? '', FILTER_VALIDATE_FLOAT);
            $categoria_id = filter_var($_POST['categoria_id'] ?? '', FILTER_VALIDATE_INT);
            $descripcion = trim($_POST['descripcion'] ?? ''); // Nuevo campo

            // Obtener datos actuales del producto para comparación y manejo de fotos
            $productoExistente = $this->modeloProducto->buscarPorId($id);
            if (!$productoExistente) {
                // Si el producto no existe, redirigir o mostrar error
                header("Location: index.php?action=listar");
                exit();
            }

            // Repoblar $producto con los datos enviados para la vista si hay errores
            $producto = [
                'id' => $id,
                'nombre' => $nombre,
                'precio' => $_POST['precio'] ?? '', // Mantener el string original para el input type="number"
                'categoria_id' => $_POST['categoria_id'] ?? '',
                'descripcion' => $descripcion,
                'foto' => $productoExistente['foto'] // Mantener la foto existente por defecto
            ];

            // --- Validación del lado del servidor (similar a registrar) ---
            if ($id === false || $id <= 0) {
                $errors['id'] = "ID de producto inválido.";
            }
            if (empty($nombre)) {
                $errors['nombre'] = "El nombre del producto es obligatorio.";
            } elseif (strlen($nombre) > 100) {
                $errors['nombre'] = "El nombre no debe exceder los 100 caracteres.";
            }
            if ($precio === false || $precio <= 0) {
                $errors['precio'] = "El precio debe ser un número válido y mayor que cero.";
            }
            if ($categoria_id === false || $categoria_id <= 0) {
                $errors['categoria_id'] = "Debe seleccionar una categoría válida.";
            }
            if (strlen($descripcion) > 500) {
                $errors['descripcion'] = "La descripción no debe exceder los 500 caracteres.";
            }

            // Manejo de la foto en edición
            $fotoActual = $productoExistente['foto']; // La foto que ya está en la BD
            $nuevaFoto = null;

            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['foto']['tmp_name'];
                $fileName = $_FILES['foto']['name'];
                $fileSize = $_FILES['foto']['size'];
                $fileType = $_FILES['foto']['type'];
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));

                $allowedfileExtensions = ['jpg', 'gif', 'png', 'jpeg'];
                if (!in_array($fileExtension, $allowedfileExtensions)) {
                    $errors['foto'] = "Tipo de archivo de imagen no permitido. Solo JPG, JPEG, PNG, GIF.";
                } elseif ($fileSize > 5000000) { // Límite de 5MB
                    $errors['foto'] = "El tamaño de la imagen no debe exceder los 5MB.";
                } else {
                    $nuevaFoto = uniqid() . "." . $fileExtension;
                    $rutaDestino = APP_PATH . "/imagenes/" . $nuevaFoto;

                    if (move_uploaded_file($fileTmpPath, $rutaDestino)) {
                        // Si se subió una nueva foto, eliminar la anterior si existe
                        if (!empty($fotoActual) && file_exists(APP_PATH . "/imagenes/" . $fotoActual)) {
                            unlink(APP_PATH . "/imagenes/" . $fotoActual);
                        }
                        $fotoActual = $nuevaFoto; // Actualiza la foto a guardar en la BD
                    } else {
                        $errors['foto'] = "Error al subir la nueva imagen.";
                    }
                }
            } elseif (isset($_FILES['foto']) && $_FILES['foto']['error'] != UPLOAD_ERR_NO_FILE) {
                $errors['foto'] = "Error en la subida del archivo: " . $_FILES['foto']['error'];
            }
            // Si no se subió una nueva foto y no hay error, se mantiene la foto actual ($fotoActual)

            // Si no hay errores, proceder con la actualización
            if (empty($errors)) {
                // Se asume que el método actualizar en ProductoDAO.php acepta 'descripcion'
                $this->modeloProducto->actualizar($id, $nombre, $precio, $fotoActual, $descripcion, $categoria_id);
                // Opcional: Mensaje de éxito
                // $_SESSION['message'] = ['type' => 'success', 'text' => 'Producto actualizado con éxito!'];
                header("Location: index.php?action=listar");
                exit();
            }
            // Si hay errores, la vista se cargará con $errors y $producto (datos enviados)
        } else {
            // Si es una solicitud GET para mostrar el formulario de edición
            $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
            if ($id === false || $id <= 0) {
                header("Location: index.php?action=listar"); // Redirigir si ID inválido
                exit();
            }
            $producto = $this->modeloProducto->buscarPorId($id);
            if (!$producto) {
                header("Location: index.php?action=listar"); // Redirigir si producto no encontrado
                exit();
            }
            // Los datos del producto se pasan a la vista para rellenar el formulario
        }
        // Incluir la vista del formulario (sea para registro o edición)
        include APP_PATH . "/admin/vista/formulario.php";
    }
}