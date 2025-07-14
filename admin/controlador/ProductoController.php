<?php

// Verifica que solo usuarios autenticados accedan
/*session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php?action=login");
    exit();
}*/

require_once APP_PATH . "/admin/modelo/ProductoDAO.php";

class ProductoController
{
    private $modelo;

    public function __construct()
    {
        $this->modelo = new ProductoDAO();
    }

    public function registrar()
    {
        require_once "modelo/CategoriaDAO.php";
        $categoriaDAO = new CategoriaDAO();
        $categorias = $categoriaDAO->listar();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'];
            $precio = $_POST['precio'];
            $categoria_id = $_POST['categoria_id'];

            // Manejo del archivo subido
            $foto = null;
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                $nombreArchivo = uniqid() . "_" . basename($_FILES['foto']['name']);
                $rutaDestino = "../imagenes/" . $nombreArchivo;

                if (move_uploaded_file($_FILES['foto']['tmp_name'], $rutaDestino)) {
                    $foto = $nombreArchivo;
                }
            }

            $this->modelo->insertar($nombre, $precio, $foto, $categoria_id);
            header("Location: index.php?action=listar");
            exit();
        }

        // Mostrar el formulario con categorías
        include "vista/formulario.php";
    }


    public function listar()
    {
        $productos = $this->modelo->listar();
        include "vista/lista.php";
    }

    public function eliminar()
    {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $this->modelo->eliminar($id);
        }
        header("Location: index.php?action=listar");
        exit();
    }

    public function editar()
    {
        require_once "modelo/CategoriaDAO.php";
        $categoriaDAO = new CategoriaDAO();
        $categorias = $categoriaDAO->listar();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $nombre = $_POST['nombre'];
            $precio = $_POST['precio'];
            $categoria_id = $_POST['categoria_id'];

            // 🔍 Traer los datos actuales del producto (incluye imagen)
            $producto = $this->modelo->buscarPorId($id);

            $foto = null;
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
                $nombreArchivo = uniqid() . "_" . basename($_FILES['foto']['name']);
                $ruta = "../imagenes/" . $nombreArchivo;

                if (move_uploaded_file($_FILES['foto']['tmp_name'], $ruta)) {
                    // Eliminar imagen anterior
                    if (!empty($producto['foto']) && file_exists("imagenes/" . $producto['foto'])) {
                        unlink("imagenes/" . $producto['foto']);
                    }
                    $foto = $nombreArchivo;
                }
            }

            $this->modelo->actualizar($id, $nombre, $precio, $foto, $categoria_id);
            header("Location: index.php?action=listar");
            exit();
        } else {
            $id = $_GET['id'];
            $producto = $this->modelo->buscarPorId($id);
            include "vista/editar.php";
        }
    }


}
