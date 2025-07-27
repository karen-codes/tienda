<?php

// Se define la ruta base de la aplicación si no está definida.
if (!defined('APP_PATH')) {
    define('APP_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once APP_PATH . "/admin/modelo/ProductoDAO.php";
require_once APP_PATH . "/admin/modelo/CategoriaDAO.php"; // Asegurarse de que CategoriaDAO esté disponible
require_once APP_PATH . "/config/conexion.php"; // Asegurarse de que la conexión esté disponible
require_once APP_PATH . "/config/csrf.php"; // Incluye la utilidad CSRF

class ProductoController
{
    private $modeloProducto;
    private $modeloCategoria;

    public function __construct()
    {
        $this->modeloProducto = new ProductoDAO();
        $this->modeloCategoria = new CategoriaDAO(); // Instanciar aquí si se usa en varios métodos
    }

    /**
     * Muestra el formulario de registro de producto y procesa su envío.
     * Incluye validación del lado del servidor y protección CSRF.
     */
    public function registrar()
    {
        $categorias = $this->modeloCategoria->listar();

        $errors = [];
        $productoData = []; // Para mantener los datos del formulario si hay errores

        // Generar el token CSRF antes de mostrar el formulario (GET o POST con errores)
        $csrf_token = generate_csrf_token(); 

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // La validación del token CSRF para POST ya se realiza en admin/index.php
            // Si llegamos aquí, el token ya fue validado.

            // Recoger y sanear los datos del formulario
            $nombre = trim($_POST['nombre'] ?? '');
            $precio = filter_var($_POST['precio'] ?? '', FILTER_VALIDATE_FLOAT);
            $categoria_id = filter_var($_POST['categoria_id'] ?? '', FILTER_VALIDATE_INT);
            $descripcion = trim($_POST['descripcion'] ?? '');

            // Guardar los datos en $productoData para repoblar el formulario en caso de error
            $productoData = [
                'nombre' => $nombre,
                'precio' => $_POST['precio'] ?? '',
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
            }

            if (strlen($descripcion) > 500) {
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
                } elseif ($fileSize > 5000000) {
                    $errors['foto'] = "El tamaño de la imagen no debe exceder los 5MB.";
                } else {
                    $nombreArchivo = uniqid() . "." . $fileExtension;
                    $rutaDestino = APP_PATH . "/imagenes/" . $nombreArchivo;

                    if (!move_uploaded_file($_FILES['foto']['tmp_name'], $rutaDestino)) {
                        $errors['foto'] = "Error al subir la imagen.";
                    } else {
                        $foto = $nombreArchivo;
                    }
                }
            } elseif (isset($_FILES['foto']) && $_FILES['foto']['error'] != UPLOAD_ERR_NO_FILE) {
                $errors['foto'] = "Error en la subida del archivo: " . $_FILES['foto']['error'];
            }

            // Si no hay errores, proceder con la inserción
            if (empty($errors)) {
                $this->modeloProducto->insertar($nombre, $precio, $foto, $descripcion, $categoria_id);
                $_SESSION['message'] = ['type' => 'success', 'text' => 'Producto registrado con éxito!'];
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
        // La validación del token CSRF para DELETE (si se usa POST) ya se realiza en admin/index.php
        // Si se usa GET para eliminar, considera añadir un token CSRF también o cambiar a POST.
        // Por ahora, solo se valida el ID.
        if (isset($_GET['id'])) {
            $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
            if ($id !== false && $id > 0) {
                $producto = $this->modeloProducto->buscarPorId($id);
                if ($producto && !empty($producto['foto'])) {
                    $rutaFoto = APP_PATH . "/imagenes/" . $producto['foto'];
                    if (file_exists($rutaFoto)) {
                        unlink($rutaFoto);
                    }
                }
                $this->modeloProducto->eliminar($id);
                $_SESSION['message'] = ['type' => 'success', 'text' => 'Producto eliminado con éxito!'];
            } else {
                $_SESSION['message'] = ['type' => 'danger', 'text' => 'ID de producto inválido para eliminar.'];
            }
        }
        header("Location: index.php?action=listar");
        exit();
    }

    /**
     * Muestra el formulario de edición de producto y procesa su envío.
     * Incluye validación del lado del servidor y protección CSRF.
     */
    public function editar()
    {
        $categorias = $this->modeloCategoria->listar();

        $errors = [];
        $producto = null; // Variable para los datos del producto a editar

        // Generar el token CSRF antes de mostrar el formulario (GET o POST con errores)
        $csrf_token = generate_csrf_token(); 

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // La validación del token CSRF para POST ya se realiza en admin/index.php
            // Si llegamos aquí, el token ya fue validado.

            // Recoger y sanear los datos del formulario POST
            $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
            $nombre = trim($_POST['nombre'] ?? '');
            $precio = filter_var($_POST['precio'] ?? '', FILTER_VALIDATE_FLOAT);
            $categoria_id = filter_var($_POST['categoria_id'] ?? '', FILTER_VALIDATE_INT);
            $descripcion = trim($_POST['descripcion'] ?? '');

            // Obtener datos actuales del producto para comparación y manejo de fotos
            $productoExistente = $this->modeloProducto->buscarPorId($id);
            if (!$productoExistente) {
                $_SESSION['message'] = ['type' => 'danger', 'text' => 'El producto a editar no existe.'];
                header("Location: index.php?action=listar");
                exit();
            }

            // Repoblar $producto con los datos enviados para la vista si hay errores
            $producto = [
                'id' => $id,
                'nombre' => $nombre,
                'precio' => $_POST['precio'] ?? '',
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
            $fotoActual = $productoExistente['foto'];
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
                } elseif ($fileSize > 5000000) {
                    $errors['foto'] = "El tamaño de la imagen no debe exceder los 5MB.";
                } else {
                    $nuevaFoto = uniqid() . "." . $fileExtension;
                    $rutaDestino = APP_PATH . "/imagenes/" . $nuevaFoto;

                    if (move_uploaded_file($fileTmpPath, $rutaDestino)) {
                        if (!empty($fotoActual) && file_exists(APP_PATH . "/imagenes/" . $fotoActual)) {
                            unlink(APP_PATH . "/imagenes/" . $fotoActual);
                        }
                        $fotoActual = $nuevaFoto;
                    } else {
                        $errors['foto'] = "Error al subir la nueva imagen.";
                    }
                }
            } elseif (isset($_FILES['foto']) && $_FILES['foto']['error'] != UPLOAD_ERR_NO_FILE) {
                $errors['foto'] = "Error en la subida del archivo: " . $_FILES['foto']['error'];
            }
            
            // Si no hay errores, proceder con la actualización
            if (empty($errors)) {
                $this->modeloProducto->actualizar($id, $nombre, $precio, $fotoActual, $descripcion, $categoria_id);
                $_SESSION['message'] = ['type' => 'success', 'text' => 'Producto actualizado con éxito!'];
                header("Location: index.php?action=listar");
                exit();
            }
        } else {
            // Si es una solicitud GET para mostrar el formulario de edición
            $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
            if ($id === false || $id <= 0) {
                $_SESSION['message'] = ['type' => 'danger', 'text' => 'ID de producto inválido para editar.'];
                header("Location: index.php?action=listar");
                exit();
            }
            $producto = $this->modeloProducto->buscarPorId($id);
            if (!$producto) {
                $_SESSION['message'] = ['type' => 'danger', 'text' => 'Producto no encontrado para editar.'];
                header("Location: index.php?action=listar");
                exit();
            }
            // Los datos del producto se pasan a la vista para rellenar el formulario
        }
        // Incluir la vista del formulario (sea para registro o edición)
        include APP_PATH . "/admin/vista/editar.php"; // Se cambió a editar.php para claridad
    }
}
